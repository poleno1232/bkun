package dto

import (
	"errors"
	"strconv"
)

type Broadcast struct {
	questId string
	msg     []byte
}

func NewBroadcast(questId string, msg []byte) (*Broadcast, error) {
	if questId == "" {
		return nil, errors.New("empty questId")
	}
	_, err := strconv.Atoi(questId)
	if err != nil {
		return nil, errors.New("incorrect questId")
	}
	return &Broadcast{questId: questId, msg: msg}, nil
}

func (b Broadcast) QuestId() string {
	return b.questId
}

func (b Broadcast) Msg() []byte {
	return b.msg
}
