const { getDefaultConfig } = require('expo/metro-config');
const http = require('http');

const config = getDefaultConfig(__dirname);

config.server = {
  enhanceMiddleware: (middleware) => {
    return (req, res, next) => {
      if (req.method === 'OPTIONS' && req.url && req.url.startsWith('/api/')) {
        res.setHeader('Access-Control-Allow-Origin', '*');
        res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
        res.setHeader('Access-Control-Allow-Headers', 'Content-Type,Authorization,Accept');
        res.writeHead(204);
        res.end();
        return;
      }

      if (req.url && req.url.startsWith('/api/')) {
        const options = {
          hostname: '127.0.0.1',
          port: 8000,
          path: req.url,
          method: req.method,
          headers: { ...req.headers, host: '127.0.0.1:8000' },
        };
        const proxyReq = http.request(options, (proxyRes) => {
          res.setHeader('Access-Control-Allow-Origin', '*');
          res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
          res.setHeader('Access-Control-Allow-Headers', 'Content-Type,Authorization,Accept');
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

      return middleware(req, res, next);
    };
  },
};

module.exports = config;
