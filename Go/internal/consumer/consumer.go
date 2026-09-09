package consumer

import (
	"context"
	"errors"
	"fmt"
	"log/slog"

	"github.com/twmb/franz-go/pkg/kgo"

	"chat/internal/config"
	"chat/internal/processor"
)

// Consumer wraps the franz-go client and the message processor.
type Consumer struct {
	cfg       config.KafkaConfig
	processor processor.Processor
	client    *kgo.Client
	logger    *slog.Logger
}

// New creates a new Consumer.
func New(cfg config.KafkaConfig, proc processor.Processor, logger *slog.Logger) (*Consumer, error) {
	// franz-go configuration
	opts := []kgo.Opt{
		kgo.SeedBrokers(cfg.Brokers...),
		kgo.ConsumerGroup(cfg.GroupID),
		kgo.ConsumeTopics(cfg.Topic),
		kgo.AutoCommitMarks(), // We will mark records as processed for auto-commit
	}

	client, err := kgo.NewClient(opts...)
	if err != nil {
		return nil, fmt.Errorf("failed to create kafka client: %w", err)
	}

	if err := client.Ping(context.Background()); err != nil {
		client.Close()
		return nil, fmt.Errorf("failed to ping kafka brokers: %w", err)
	}

	return &Consumer{
		cfg:       cfg,
		processor: proc,
		client:    client,
		logger:    logger,
	}, nil
}

func (c *Consumer) Run(ctx context.Context) error {
	c.logger.InfoContext(ctx, "Starting consumer", "topic", c.cfg.Topic, "group", c.cfg.GroupID)
	defer c.Close()

	for {
		fetches := c.client.PollRecords(ctx, 100)

		if fetches.IsClientClosed() {
			return errors.New("kafka client closed")
		}

		if errs := fetches.Errors(); len(errs) > 0 {
			for _, err := range errs {
				c.logger.ErrorContext(ctx, "Kafka fetch error", "error", err.Err, "topic", err.Topic)
			}
			continue
		}

		for _, record := range fetches.Records() {
			msg := processor.Message{
				Key:       record.Key,
				Value:     record.Value,
				Topic:     record.Topic,
				Partition: record.Partition,
				Offset:    record.Offset,
			}

			if err := c.processor.Process(ctx, msg); err != nil {
				c.logger.ErrorContext(ctx, "Failed to process message", "error", err, "offset", msg.Offset)
			} else {
				// Mark the record as successfully processed so it can be auto-committed
				c.client.MarkCommitRecords(record)
			}
		}
	}
}

// Close gracefully shuts down the Kafka client.
func (c *Consumer) Close() {
	c.logger.Info("Closing Kafka consumer")
	c.client.Close()
}
