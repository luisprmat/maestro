#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m'

HOST=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --host=*)
            HOST="${1#*=}"
            shift
            ;;
        --host)
            HOST="$2"
            shift 2
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            exit 1
            ;;
    esac
done

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BUILD_DIR="$ROOT_DIR/build"

if [ ! -d "$BUILD_DIR" ]; then
    echo -e "${RED}The build folder does not exist. Please run 'php artisan build' first.${NC}"
    exit 1
fi

cd "$BUILD_DIR" || exit 1

echo -e "${BLUE}Running composer setup...${NC}"
composer setup

if [ -z "$HOST" ]; then
    HOST="http://localhost:8000"
fi

if [ -f ".env" ]; then
    sed -i "s|APP_URL=http://localhost|APP_URL=$HOST|g" .env
    echo -e "${GREEN}Updated APP_URL to ${YELLOW}$HOST${NC}"
fi

echo -e "${BLUE}Starting development server...${NC}"
composer dev
