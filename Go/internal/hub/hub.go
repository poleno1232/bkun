package hub

import (
	"chat/internal/dto"
	"context"
	"fmt"
	"sync"
	"time"

	"github.com/coder/websocket"
)

type Hub struct {
	clients    map[string]map[*dto.Client]struct{} // looks like an abomination, not actually a one
	broadcast  chan *dto.Broadcast
	register   chan *dto.Client
	unregister chan *dto.Client
	mu         sync.Mutex
}

func NewHub() *Hub {
	return &Hub{
		clients:    make(map[string]map[*dto.Client]struct{}),
		broadcast:  make(chan *dto.Broadcast, 256),
		register:   make(chan *dto.Client),
		unregister: make(chan *dto.Client),
	}
}

func (h *Hub) Broadcast(msg *dto.Broadcast) {
	h.broadcast <- msg
}

func (h *Hub) Register(client *dto.Client) {
	h.register <- client
}

func (h *Hub) Unregister(client *dto.Client) {
	h.unregister <- client
}

func (h *Hub) ConnectionCount() int {
	return len(h.clients)
}

func (h *Hub) Run(ctx context.Context) {
	fmt.Println("Hub run")

	for {
		select {
		case <-ctx.Done():
			h.mu.Lock()
			for questId, clients := range h.clients {
				for c := range clients {
					c.CloseNow()
				}
				delete(h.clients, questId)
			}
			h.mu.Unlock()
			return
		case client := <-h.register:
			h.mu.Lock()
			questId := client.QuestId()
			if h.clients[questId] == nil {
				h.clients[questId] = make(map[*dto.Client]struct{})
			}
			h.clients[questId][client] = struct{}{}
			h.mu.Unlock()
			fmt.Println("New registered client")
		case client := <-h.unregister:
			h.mu.Lock()
			questId := client.QuestId()
			if clients, ok := h.clients[questId]; ok {
				delete(clients, client)

				if len(clients) == 0 {
					delete(h.clients, questId)
				}
			}
			h.mu.Unlock()

			client.CloseNow()
			fmt.Println("Client is disconnected")
		case msg := <-h.broadcast:
			h.mu.Lock()
			clients, exists := h.clients[msg.QuestId()]
			if !exists {
				h.mu.Unlock()
				continue // No one in this quest, skip
			}

			targets := make([]*dto.Client, 0, len(clients))
			for c := range clients {
				targets = append(targets, c)
			}
			h.mu.Unlock()

			for _, c := range targets {
				go func(conn *dto.Client) {
					ioCtx, cancel := context.WithTimeout(context.Background(), 2*time.Second)
					defer cancel()
					err := conn.Write(ioCtx, websocket.MessageText, msg.Msg())

					if err != nil {
						h.Unregister(conn)
					}
				}(c)
			}
		}
	}
}
