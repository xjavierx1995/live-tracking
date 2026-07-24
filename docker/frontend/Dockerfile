FROM node:24-alpine

# ── pnpm ─────────────────────────────────────
RUN npm install -g pnpm

# ── Install curl for healthcheck ─────────────
RUN apk add --no-cache curl

# El usuario node ya existe con UID 1000 en node:alpine
# Usar ese usuario directamente
RUN mkdir -p /app && chown -R node:node /app

WORKDIR /app

# ── Copiar package.json primero (cache layer) ──────────
COPY --chown=node:node frontend/package.json frontend/pnpm-lock.yaml* ./

# ── Instalar dependencias ────────────────────
USER node
ENV CI=true
RUN pnpm install --frozen-lockfile --ignore-scripts

# ── Copiar resto del código ──────────────────
COPY --chown=node:node frontend/ ./

EXPOSE 5173

CMD ["pnpm", "dev", "--host"]