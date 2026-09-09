package processor

import (
	"chat/internal/config"
	"chat/internal/hub"
	"context"
	"encoding/json"
	"log"
	"net/http"
	"time"

	"github.com/coder/websocket"
	"golang.org/x/time/rate"
)

type ChatServer struct {
	subscriberMessageBuffer int
	publishLimiter          *rate.Limiter
	hub                     *hub.Hub
	ctx                     context.Context

	logf func(f string, v ...any)
}

func NewChatServer(cfg config.WebSocketConfig, hub *hub.Hub, ctx context.Context) *ChatServer {
	cs := &ChatServer{
		subscriberMessageBuffer: cfg.SubscriberMessageBuffer,
		logf:                    log.Printf,
		publishLimiter:          cfg.PublishRateLimiter,
		hub:                     hub,
		ctx:                     ctx,
	}

	return cs
}

func (cs *ChatServer) HandleConnection(w http.ResponseWriter, r *http.Request) {
	conn, err := websocket.Accept(w, r, &websocket.AcceptOptions{
		OriginPatterns: []string{"*"},
	})
	if err != nil {
		log.Println("Accept error:", err)
		return
	}
	defer conn.Close(websocket.StatusInternalError, "internal error")

	cs.hub.Register(conn)

	cs.keepAlive(conn, 30*time.Second)
}

func (cs *ChatServer) keepAlive(c *websocket.Conn, interval time.Duration) {
	ticker := time.NewTicker(interval)
	defer ticker.Stop()

	go func() {
		for {
			_, _, err := c.Read(cs.ctx)
			if err != nil {
				return
			}
		}
	}()

	for {
		select {
		case <-cs.ctx.Done():
			return
		case <-ticker.C:
			// Give each ping operation a short timeout
			pingCtx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
			err := c.Ping(pingCtx)
			cancel()

			if err != nil {
				log.Printf("ping failed: %v", err)
				cs.hub.Unregister(c)
				return
			}

			marshal, err := json.Marshal(map[string]interface{}{
				"type":  "counter",
				"count": cs.hub.ConnectionCount(),
			})
			if err != nil {
				continue
			}

			c.Write(cs.ctx, websocket.MessageText, marshal)
		}
	}
}
