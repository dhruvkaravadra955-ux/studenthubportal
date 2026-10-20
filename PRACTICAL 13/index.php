<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student CRUD using Fetch API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Student Management</h1>
    <p class="intro">PHP JSON API and Fetch API CRUD without page reload</p>

    <div id="message" class="message" role="status" aria-live="polite"></div>

    <section class="panel">
        <h2 id="formTitle">Add Student</h2>
        <form id="studentForm">
            <input type="hidden" id="studentId">
            <label for="name">Name</label>
            <input id="name" maxlength="100" required placeholder="Enter student name">

            <label for="email">Email</label>
            <input id="email" type="email" maxlength="150" required placeholder="Enter email">

            <label for="course">Course</label>
            <select id="course" required>
                <option value="">Select course</option>
                <option>BCA</option>
                <option>BSc IT</option>
                <option>BTech</option>
            </select>

            <button type="submit" id="saveButton">Add Student</button>
            <button type="button" id="cancelButton" class="secondary" hidden>Cancel Edit</button>
        </form>
    </section>

    <section class="panel">
        <h2>Student Records</h2>
        <label for="search">Search by name, email, or course</label>
        <input id="search" placeholder="Type to search...">
        <p id="loading" hidden>Loading students...</p>
        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th></tr></thead>
                <tbody id="studentRows">
                    <tr><td colspan="5">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<script src="script.js"></script>
</body>
</html>