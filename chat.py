from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

messages = []

@app.route('/messages', methods=['GET'])
def get_messages():
    return jsonify({'messages': messages})

@app.route('/send', methods=['POST'])
def send_message():
    global messages
    data = request.get_json()
    username = data.get('username', f'Anonymous{len(messages) + 1}')
    message = data.get('message')
    color = data.get('color', 'black')
    if message:
        messages.append({'username': username, 'message': message, 'color': color})
        if len(messages) > 100:
            messages = messages[-100:]  # Limit to last 100 messages
    return jsonify({'status': 'success'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
