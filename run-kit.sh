#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BUILD_DIR="$ROOT_DIR/build"

if [ ! -d "$BUILD_DIR" ]; then
    echo -e "${RED}The build folder does not exist. Please run 'php artisan build' first.${NC}"
    exit 1
fi

cd "$BUILD_DIR" || exit 1

echo -e "${BLUE}Running composer setup...${NC}"
composer setup

if [ -f ".env" ]; then
    sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8000|g' .env

    # Copy WorkOS credentials from root .env if they exist
    if [ -f "$ROOT_DIR/.env" ]; then
        WORKOS_CLIENT_ID=$(grep -E '^WORKOS_CLIENT_ID=' "$ROOT_DIR/.env" | cut -d '=' -f2-)
        WORKOS_API_KEY=$(grep -E '^WORKOS_API_KEY=' "$ROOT_DIR/.env" | cut -d '=' -f2-)

        if [ -n "$WORKOS_CLIENT_ID" ] && [ -n "$WORKOS_API_KEY" ]; then
            echo -e "${BLUE}Copying WorkOS credentials from root .env...${NC}"
            sed -i "s|^WORKOS_CLIENT_ID=.*|WORKOS_CLIENT_ID=$WORKOS_CLIENT_ID|g" .env
            sed -i "s|^WORKOS_API_KEY=.*|WORKOS_API_KEY=$WORKOS_API_KEY|g" .env
        fi
    fi
fi

echo -e "${GREEN}Starting development server...${NC}"
composer dev
