const api = "api/students.php";

const form = document.getElementById("studentForm");
const idInput = document.getElementById("studentId");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const courseInput = document.getElementById("course");
const rows = document.getElementById("studentRows");
const message = document.getElementById("message");
const loading = document.getElementById("loading");
const searchInput = document.getElementById("search");
const saveButton = document.getElementById("saveButton");
const cancelButton = document.getElementById("cancelButton");
let searchTimer;

function showMessage(text, type = "success") {
    message.textContent = text;
    message.className = "message " + type;
    message.hidden = false;
}

async function request(url, options = {}) {
    const response = await fetch(url, options);
    let result;
    try {
        result = await response.json();
    } catch {
        throw new Error("Server did not return valid JSON.");
    }
    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }
    return result;
}

async function loadStudents(search = "") {
    loading.hidden = false;
    try {
        const result = await request(api + "?search=" + encodeURIComponent(search));
        rows.replaceChildren();

        if (!result.data.length) {
            const tr = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan = 5;
            td.textContent = "No students found.";
            tr.appendChild(td);
            rows.appendChild(tr);
            return;
        }

        result.data.forEach(student => {
            const tr = document.createElement("tr");
            [student.id, student.name, student.email, student.course].forEach(value => {
                const td = document.createElement("td");
                td.textContent = value;
                tr.appendChild(td);
            });

            const actionCell = document.createElement("td");
            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.className = "small";
            editButton.addEventListener("click", () => editStudent(student));

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.className = "small danger";
            deleteButton.addEventListener("click", () => deleteStudent(student.id));

            actionCell.append(editButton, deleteButton);
            tr.appendChild(actionCell);
            rows.appendChild(tr);
        });
    } catch (error) {
        rows.replaceChildren();
        showMessage(error.message, "error");
    } finally {
        loading.hidden = true;
    }
}

function editStudent(student) {
    idInput.value = student.id;
    nameInput.value = student.name;
    emailInput.value = student.email;
    courseInput.value = student.course;
    document.getElementById("formTitle").textContent = "Update Student";
    saveButton.textContent = "Update Student";
    cancelButton.hidden = false;
    nameInput.focus();
}

function resetForm() {
    form.reset();
    idInput.value = "";
    document.getElementById("formTitle").textContent = "Add Student";
    saveButton.textContent = "Add Student";
    cancelButton.hidden = true;
}

form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const data = {
        action: idInput.value ? "update" : "add",
        id: idInput.value,
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value
    };

    saveButton.disabled = true;
    try {
        const result = await request(api, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        showMessage(result.message, "success");
        resetForm();
        await loadStudents(searchInput.value);
    } catch (error) {
        showMessage(error.message, "error");
    } finally {
        saveButton.disabled = false;
    }
});

async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) return;

    try {
        const result = await request(api, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });
        showMessage(result.message, "success");
        await loadStudents(searchInput.value);
    } catch (error) {
        showMessage(error.message, "error");
    }
}

cancelButton.addEventListener("click", resetForm);

searchInput.addEventListener("input", function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadStudents(searchInput.value), 300);
});

loadStudents();