import telebot

# Ganti TOKEN_BOT_ANDA dengan token bot Telegram Anda
TOKEN = '7095664056:AAEiPyzvnPYcWBk2SVNgfYHk0TgRWaa-g4c'
bot = telebot.TeleBot(TOKEN)

# Menangani perintah /start
@bot.message_handler(commands=['start'])
def send_welcome(message):
    bot.reply_to(message, "Ok")

# Menangani pesan yang diterima
@bot.message_handler(func=lambda _: True)
def echo_all(message):
    bot.reply_to(message, message.text)

# Jalankan bot
bot.polling()
