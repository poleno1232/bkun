package config

import (
	"os"
	"strconv"
	"strings"
	"time"

	"golang.org/x/time/rate"
)

type KafkaConfig struct {
	Brokers         []string
	GroupID         string
	Topic           string
	AutoOffsetReset string
	Timeout         int
}

type WebSocketConfig struct {
	SubscriberMessageBuffer int
	PublishRateLimiter      *rate.Limiter
}

func Load() (KafkaConfig, WebSocketConfig) {
	brokers := os.Getenv("KAFKA_BROKERS")
	if brokers == "" {
		brokers = "kafka:9092"
	}
	timeout, err := strconv.Atoi(getEnvOrDefault("KAFKA_TIMEOUT", "100"))

	if err != nil {
		timeout = 100
	}

	return KafkaConfig{
			Brokers:         strings.Split(brokers, ","),
			GroupID:         getEnvOrDefault("KAFKA_GROUP_ID", "my-go-consumer-group"),
			Topic:           getEnvOrDefault("KAFKA_TOPIC", "my-topic"),
			AutoOffsetReset: getEnvOrDefault("KAFKA_AUTO_OFFSET_RESET", "earliest"),
			Timeout:         timeout,
		}, WebSocketConfig{
			SubscriberMessageBuffer: 100,
			PublishRateLimiter:      rate.NewLimiter(rate.Every(time.Millisecond*100), 8),
		}
}

func getEnvOrDefault(key, defaultValue string) string {
	if value, exists := os.LookupEnv(key); exists {
		return value
	}
	return defaultValue
}
