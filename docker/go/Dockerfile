ARG GO_VERSION
FROM golang:${GO_VERSION}-alpine
ARG GO_VERSION
ARG GO_APP_NAME
RUN if [ -z "$GO_APP_NAME" ]; then echo "ERROR: GO_APP_NAME build-arg is required" && exit 1; fi
RUN if [ -z "$GO_VERSION" ]; then echo "ERROR: GO_VERSION build-arg is required" && exit 1; fi

COPY ./Go/go.mod /usr/src/app/go.mod
WORKDIR /usr/src/app

RUN GOMOD_VERSION=$(grep -E '^go [0-9]+\.[0-9]+' go.mod | awk '{print $2}') && \
    echo "go.mod version: |$GOMOD_VERSION|" && \
    echo "Docker Go version: |$GO_VERSION|" && \
    if [ $GO_VERSION != ${CURRENT_VERSION} ]; then \
        echo "ERROR: Version mismatch! Please update your Docker base image." && exit 1; \
    fi
# pre-copy/cache go.mod for pre-downloading dependencies and only redownloading them in subsequent builds if they change
RUN go mod tidy
RUN go mod download

COPY ./Go /usr/src/app

RUN go mod tidy
RUN go mod download

RUN mkdir -p -v ./logs
RUN go build -v -o /usr/local/bin/app './cmd'

CMD ["app"]