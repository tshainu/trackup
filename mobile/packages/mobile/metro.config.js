const { getDefaultConfig } = require('expo/metro-config');
const http = require('http');

const config = getDefaultConfig(__dirname);

// Proxy /api/* requests to the Laravel backend (avoids mixed-content on https preview)
const BACKEND = 'http://69.169.97.195:8080';

config.server = {
  enhanceMiddleware: (middleware) => {
    return (req, res, next) => {
      if (req.url && req.url.startsWith('/api/')) {
        // Proxy to backend
        const options = {
          hostname: '69.169.97.195',
          port: 8080,
          path: req.url,
          method: req.method,
          headers: {
            ...req.headers,
            host: '69.169.97.195:8080',
          },
        };

        const proxyReq = http.request(options, (proxyRes) => {
          // Pass CORS headers for preview
          const origin = req.headers.origin;
          if (origin && origin.includes('runable.site')) {
            res.setHeader('Access-Control-Allow-Origin', origin);
            res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
            res.setHeader('Access-Control-Allow-Headers', 'Content-Type,Authorization,Accept');
          }
          res.writeHead(proxyRes.statusCode, proxyRes.headers);
          proxyRes.pipe(res, { end: true });
        });

        proxyReq.on('error', (err) => {
          console.error('Proxy error:', err);
          res.writeHead(502);
          res.end('Bad Gateway');
        });

        req.pipe(proxyReq, { end: true });
        return;
      }

      // Handle OPTIONS preflight for /api/*
      if (req.method === 'OPTIONS' && req.url && req.url.startsWith('/api/')) {
        const origin = req.headers.origin;
        if (origin && origin.includes('runable.site')) {
          res.setHeader('Access-Control-Allow-Origin', origin);
          res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
          res.setHeader('Access-Control-Allow-Headers', 'Content-Type,Authorization,Accept');
        }
        res.writeHead(204);
        res.end();
        return;
      }

      return middleware(req, res, next);
    };
  },
};

module.exports = config;
