from flask import Flask, request, jsonify
import subprocess
import base64

app = Flask(__name__)

@app.route('/execute', methods=['POST'])
def execute_command():
    data = request.get_json()
    command = data.get('command')
    commandbase64 = base64.b64encode(command.encode())
    base64_string = commandbase64.decode()
    print(commandbase64)
    print(base64_string)
    server = data.get('server')
    magdir = data.get('dir')
    bashscript = "/root/commands.sh -c \"" + base64_string + "\" -s \"" + server + "\" -d \"" + magdir + "\""
    print(bashscript)
    
    try:
        result = subprocess.run(bashscript, shell=True, text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        
        if result.returncode == 0:
            response = {
                'success': True,
                'stdout': result.stdout
            }
        else:
            response = {
                'success': False,
                'stderr': result.stderr
            }
    except subprocess.CalledProcessError as e:
        response = {
            'success': False,
            'error': str(e)
        }
    
    return jsonify(response)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

#if __name__ == '__main__':
#    app.run(debug=True)
