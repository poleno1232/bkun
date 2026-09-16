package dto

import (
	"context"
	"strconv"

	"github.com/coder/websocket"
)

type Client struct {
	conn    *websocket.Conn
	questId int
}

func NewClient(conn *websocket.Conn, questId int) *Client {
	return &Client{
		conn:    conn,
		questId: questId,
	}
}

func (c *Client) QuestId() string {
	return strconv.Itoa(c.questId)
}

func (c *Client) Connection() *websocket.Conn {
	return c.conn
}

func (c *Client) CloseNow() {
	c.conn.CloseNow()
}

func (c *Client) Write(ctx context.Context, msgType websocket.MessageType, msg []byte) error {
	return c.conn.Write(ctx, msgType, msg)
}

func (c *Client) Read(ctx context.Context) (websocket.MessageType, []byte, error) {
	read, i, err := c.conn.Read(ctx)
	return read, i, err
}

func (c *Client) Ping(ctx context.Context) error {
	err := c.conn.Ping(ctx)

	return err
}
