package hub

import (
	"context"
	"fmt"
	"sync"
	"time"

	"github.com/coder/websocket"
)

type Hub struct {
	clients    map[*websocket.Conn]bool
	broadcast  chan []byte
	register   chan *websocket.Conn
	unregister chan *websocket.Conn
	mu         sync.Mutex
}

func NewHub() *Hub {
	return &Hub{
		clients:    make(map[*websocket.Conn]bool),
		broadcast:  make(chan []byte, 256),
		register:   make(chan *websocket.Conn),
		unregister: make(chan *websocket.Conn),
	}
}

func (h *Hub) Broadcast(msg []byte) {
	h.broadcast <- msg
}

func (h *Hub) Register(conn *websocket.Conn) {
	h.register <- conn
}

func (h *Hub) Unregister(conn *websocket.Conn) {
	h.unregister <- conn
}

func (h *Hub) ConnectionCount() int {
	return len(h.clients)
}

func (h *Hub) Run(ctx context.Context) {
	fmt.Println("Hub run")

	for {
		select {
		case <-ctx.Done():
			for c := range h.clients {
				c.CloseNow()
			}
			return
		case conn := <-h.register:
			h.mu.Lock()
			h.clients[conn] = true
			h.mu.Unlock()
			fmt.Println("New registered client")
		case conn := <-h.unregister:
			h.mu.Lock()
			delete(h.clients, conn)
			conn.CloseNow()
			h.mu.Unlock()
			fmt.Println("Client is disconnected")
		case msg := <-h.broadcast:
			h.mu.Lock()
			for c := range h.clients {
				ctx, cancel := context.WithTimeout(
					context.Background(), 5*time.Second,
				)
				err := c.Write(ctx, websocket.MessageText, msg)
				cancel()
				if err != nil {
					delete(h.clients, c)
					c.CloseNow()
				}
			}
			h.mu.Unlock()
		}
	}
}
