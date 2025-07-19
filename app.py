import os
from flask import Flask, render_template, request, redirect, url_for, flash
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user

app = Flask(__name__)
app.config['SECRET_KEY'] = 'supersecretkey'

login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'login'

class User(UserMixin):
    def __init__(self, id):
        self.id = id

@login_manager.user_loader
def load_user(user_id):
    return User(user_id)

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        if username == 'test' and password == 'test':
            user = User(username)
            login_user(user)
            return redirect(url_for('index'))
    return render_template('login.html')

@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('login'))

from gedcom.parser import Parser

@app.route('/')
@login_required
def index():
    gedcom_files = [f for f in os.listdir('data') if f.endswith('.ged')]
    return render_template('index.html', gedcom_files=gedcom_files)

@app.route('/gedcom/<gedcom_file>')
@login_required
def gedcom_index(gedcom_file):
    gedcom_path = os.path.join('data', gedcom_file)
    gedcom = Parser().parse_file(gedcom_path)

    people = []
    for element in gedcom.get_root_child_elements():
        if element.get_tag() == "INDI":
            (name, _) = element.get_name()
            people.append((element.get_pointer(), name))

    return render_template('gedcom_index.html', people=people, gedcom_file=gedcom_file)

@app.route('/gedcom/<gedcom_file>/person/<person_pointer>')
@login_required
def person_detail(gedcom_file, person_pointer):
    gedcom_path = os.path.join('data', gedcom_file)
    gedcom = Parser().parse_file(gedcom_path)

    person = gedcom.get_element_by_pointer(person_pointer)

    (name, _) = person.get_name()
    (birth_date, _) = person.get_birth_data()
    (death_date, _) = person.get_death_data()

    parents = []
    for family in gedcom.get_families(person, "CHIL"):
        for parent in gedcom.get_parents(family):
            (parent_name, _) = parent.get_name()
            parents.append((parent.get_pointer(), parent_name))

    children = []
    for family in gedcom.get_families(person):
        for child in gedcom.get_children(family):
            (child_name, _) = child.get_name()
            children.append((child.get_pointer(), child_name))

    siblings = []
    for family in gedcom.get_families(person, "CHIL"):
        for sibling in gedcom.get_children(family):
            if sibling.get_pointer() != person.get_pointer():
                (sibling_name, _) = sibling.get_name()
                siblings.append((sibling.get_pointer(), sibling_name))

    return render_template('person_detail.html', name=name, birth_date=birth_date, death_date=death_date,
                           parents=parents, children=children, siblings=siblings, gedcom_file=gedcom_file)

from werkzeug.utils import secure_filename

@app.route('/upload', methods=['POST'])
@login_required
def upload_file():
    if 'file' not in request.files:
        flash('No file part')
        return redirect(request.url)
    file = request.files['file']
    if file.filename == '':
        flash('No selected file')
        return redirect(request.url)
    if file and file.filename.endswith('.ged'):
        filename = secure_filename(file.filename)
        file.save(os.path.join('data', filename))
        flash('File successfully uploaded')
        return redirect(url_for('index'))

@app.route('/request_access', methods=['GET', 'POST'])
def request_access():
    if request.method == 'POST':
        # Here you would implement the logic to email mike@winterer.com
        # For now, we'll just redirect to the login page
        return redirect(url_for('login'))
    return render_template('request_access.html')


if __name__ == '__main__':
    app.run(debug=True)
