module.exports = {
  apps: [{
    name: "ecommerce-app",
    script: "php",
    args: "-S 0.0.0.0:8000 -t .",
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: "production",
      APP_ENV: "production"
    }
  }]
};
