package processor

import (
	"chat/internal/hub"
	"context"
)

type Message struct {
	Key       []byte
	Value     []byte
	Topic     string
	Partition int32
	Offset    int64
}

type Processor interface {
	Process(ctx context.Context, msg Message) error
}

type MessageProcessor struct {
	hub *hub.Hub
}

func NewLogProcessor(newhub *hub.Hub) *MessageProcessor {
	return &MessageProcessor{hub: newhub}
}

func (p *MessageProcessor) Process(ctx context.Context, msg Message) error {
	p.hub.Broadcast(msg.Value)

	return nil
}
