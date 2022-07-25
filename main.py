from flask import Flask, render_template, request, jsonify,  send_file, url_for,  session, redirect
import os, json, copy, statistics
from json import dumps
import pandas as pd
import io
from werkzeug.utils import secure_filename


app = Flask(__name__)



@app.route('/', methods=['GET', 'POST'])
def index():
    return render_template('index.html')


if __name__ == '__main__':

    app.config['SESSION_TYPE'] = 'filesystem'

    app.run(debug=True, host='0.0.0.0', port=5000)

