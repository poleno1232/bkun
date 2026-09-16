package processor

import (
	"chat/internal/config"
	"chat/internal/dto"
	"chat/internal/hub"
	"context"
	"encoding/json"
	"log"
	"net/http"
	"strconv"
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

	queryQuest := r.URL.Query().Get("quest_id")
	if queryQuest == "" {
		conn.Close(websocket.StatusUnsupportedData, "unsupported quest_id")
		return
	}

	questId, err := strconv.Atoi(queryQuest)
	if err != nil {
		conn.Close(websocket.StatusUnsupportedData, "unsupported quest_id")
		return
	}

	client := dto.NewClient(conn, questId)
	cs.hub.Register(client)

	cs.keepAlive(client, 30*time.Second)
}

func (cs *ChatServer) keepAlive(client *dto.Client, interval time.Duration) {
	ticker := time.NewTicker(interval)
	defer ticker.Stop()

	go func() {
		for {
			_, _, err := client.Read(cs.ctx)
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
			err := client.Ping(pingCtx)
			cancel()

			if err != nil {
				log.Printf("ping failed: %v", err)
				cs.hub.Unregister(client)
				return
			}

			marshal, err := json.Marshal(map[string]interface{}{
				"type":  "counter",
				"count": cs.hub.ConnectionCount(),
			})
			if err != nil {
				continue
			}

			err = client.Write(cs.ctx, websocket.MessageText, marshal)
			if err != nil {
				return
			}
		}
	}
}
