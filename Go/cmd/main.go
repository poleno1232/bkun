package main

import (
	"chat/internal/config"
	"chat/internal/consumer"
	"chat/internal/hub"
	"chat/internal/processor"
	"context"
	"fmt"
	"log"
	"log/slog"
	"net/http"
	"os"
	"os/signal"

	"github.com/joho/godotenv"
)

func main() {
	ctx, cancel := signal.NotifyContext(context.Background(), os.Interrupt)
	defer cancel()

	fmt.Println("Setting up server...")

	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}

	fmt.Println("Loading config...")
	kafkaCfg, wsCfg := config.Load()

	fmt.Println("Starting hub...")
	chatHub := hub.NewHub()

	fmt.Println("Starting Kafka consumer...")
	logger := slog.New(slog.NewJSONHandler(os.Stdout, nil))
	kafka, err := consumer.New(kafkaCfg, processor.NewLogProcessor(chatHub), logger)
	if err != nil {
		log.Fatal("Error establishing connection to kafka. Error: " + err.Error())
	}

	go func() {
		err := kafka.Run(ctx)
		if err != nil {
			log.Fatal("Error starting kafka. Error: " + err.Error())
		}
	}()

	server := processor.NewChatServer(wsCfg, chatHub, ctx)

	http.HandleFunc("/ws", server.HandleConnection)

	go chatHub.Run(ctx)
	err = http.ListenAndServe(":8081", nil)
	if err != nil {
		return
	}
}
