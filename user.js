function showUserManagement() {
    const content = document.getElementById('content');
    const pageTitle = document.getElementById('page-title');

    pageTitle.textContent = 'User Management';
    content.innerHTML = `
        <h2>User Management</h2>
        <button onclick="openAddUserForm()">Add User</button>
        <table border="1" cellspacing="0" cellpadding="5" style="margin-top: 20px; width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <tr>
                    <td colspan="4">Loading user data...</td>
                </tr>
            </tbody>
        </table>
    `;

    fetch('fetch_users.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const userTableBody = document.getElementById('userTableBody');
                userTableBody.innerHTML = data.users.map(user => `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.role}</td>
                        <td>
                            <button onclick="editUser(${user.id})">Edit</button>
                            <button onclick="deleteUser(${user.id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                document.getElementById('userTableBody').innerHTML = `
                    <tr>
                        <td colspan="4">Error loading user data: ${data.message}</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            document.getElementById('userTableBody').innerHTML = `
                <tr>
                    <td colspan="4">Failed to fetch user data: ${error.message}</td>
                </tr>
            `;
        });
}

function openAddUserForm() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <h2>Add New User</h2>
        <form id="addUserForm">
            <label>Username: 
                <input type="text" id="newUsername" required>
            </label><br>
            <label>Password: 
                <input type="password" id="newPassword" required>
            </label><br>
            <label>Role: 
                <select id="newRole">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </label><br>
            <button type="button" onclick="submitNewUser()">Add User</button>
        </form>
    `;
}

function editUser(userId) {
    fetch(`fetch_user.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                const content = document.getElementById('content');
                content.innerHTML = `
                    <h2>Edit User</h2>
                    <form id="editUserForm">
                        <label>Username:
                            <input type="text" id="editUsername" value="${user.username}" required>
                        </label><br>
                        <label>Role:
                            <select id="editRole">
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                            </select>
                        </label><br>
                        <button type="button" onclick="submitEditUser(${userId})">Save Changes</button>
                    </form>
                `;
            } else {
                alert(`Error fetching user data: ${data.message}`);
            }
        })
        .catch(error => {
            alert(`Error fetching user data: ${error.message}`);
        });
}

function submitEditUser(userId) {
    const username = document.getElementById('editUsername').value;
    const role = document.getElementById('editRole').value;

    fetch('edit_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: userId, username, role })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User updated successfully.');
                showUserManagement(); // Refresh the user list
            } else {
                alert(`Failed to update user: ${data.message}`);
            }
        })
        .catch(error => {
            alert(`Error updating user: ${error.message}`);
        });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`delete_user.php?id=${userId}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted successfully.');
                    showUserManagement(); // Refresh the user list
                } else {
                    alert(`Failed to delete user: ${data.message}`);
                }
            })
            .catch(error => {
                alert(`Error deleting user: ${error.message}`);
            });
    }
}

function submitNewUser() {
    const username = document.getElementById('newUsername').value;
    const password = document.getElementById('newPassword').value;
    const role = document.getElementById('newRole').value;

    fetch('add_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password, role })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User added successfully.');
                showUserManagement(); // Refresh the user list
            } else {
                alert(`Failed to add user: ${data.message}`);
            }
        })
        .catch(error => {
            alert(`Error adding user: ${error.message}`);
        });
}
