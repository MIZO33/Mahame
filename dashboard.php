<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("service-worker.js")
            .then(() => console.log("Service Worker Registered"))
            .catch(err => console.error("Service Worker Registration Failed", err));
    }
</script>
<link rel="manifest" href="manifest.json">

</head>
<body>
    <!-- Left Sidebar -->
    <nav id="sidebar">  
        <ul>
            <li><button onclick="loadPage('welcome')" class="active">Home</button></li>
            <li><button onclick="loadPage('sales')">Sales</button></li>
            <li><button onclick="loadPage('inventory')">Inventory</button></li>
            <li><button onclick="loadPage('report')">Reports</button></li>
            <li><button onclick="loadPage('manageRooms')">Manage Rooms</button></li>
        </ul>
        <!-- Logout Button -->
        <div class="logout-container">
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
    <div class="header">
        <h1 id="page-title">Welcome To The Lodge Accessories Dashboard</h1>
        <!-- User Management Button -->
        <div class="user-management">
            <button onclick="showUserManagement()">User Management</button>
        </div>
    </div>
    <div id="content">
        <p>Select a page from the left navigation bar to see and make changes you want</p>
    </div>
</main>


    <!-- JavaScript -->
    <script>
        // Function to dynamically load content and highlight active buttons
        function loadPage(page) {
            const content = document.getElementById('content');
            const pageTitle = document.getElementById('page-title');
            const buttons = document.querySelectorAll('#sidebar button');

            // Fade out content before changing
            content.style.opacity = '0';

            setTimeout(() => {
                // Remove 'active' class from all buttons
                buttons.forEach(button => button.classList.remove('active'));

                // Add 'active' class to the clicked button
                const activeButton = Array.from(buttons).find(btn => btn.textContent.toLowerCase().includes(page));
                if (activeButton) activeButton.classList.add('active');

                // Update content based on page
                switch (page) {
                    case 'welcome':
                        pageTitle.textContent = 'Welcome To The Lodge Accessories Dashboard';
                        content.innerHTML = '<p>Select a page from the left navigation bar to see and make changes you want</p>';
                        break;
                        case 'sales':
    pageTitle.textContent = 'Sales';
    content.innerHTML = `
        <h2>Record Sales</h2>
        <form id="salesForm">
            <label>Product Name: <input type="text" id="product_name" placeholder="Enter product name"></label><br>
            <label>Quantity: <input type="number" id="quantity_sold" placeholder="Enter quantity"></label><br>
            <button type="button" onclick="submitSale()">Submit Sale</button>
        </form>
        <p id="sale-response" style="color: red;"></p>
    `;

    // Add submitSale function dynamically
    const submitSale = () => {
        const productName = document.getElementById('product_name').value;
        const quantitySold = document.getElementById('quantity_sold').value;
        const responseElement = document.getElementById('sale-response');

        // Validation
        if (!productName || !quantitySold) {
            responseElement.textContent = 'Please fill in all fields.';
            return;
        }

        // Send data to PHP script
        fetch('record_sale.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_name=${encodeURIComponent(productName)}&quantity_sold=${encodeURIComponent(quantitySold)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                responseElement.textContent = `Error: ${data.error}`;
            } else {
                responseElement.textContent = data.message;
                responseElement.style.color = 'green';
            }
        })
        .catch(error => {
            responseElement.textContent = `Error: ${error}`;
        });
    };
    window.submitSale = submitSale;  // Expose function to the global scope
    break;
    case 'inventory':
    pageTitle.textContent = 'Inventory';

    // Fetch inventory data from the server
    fetch('fetch_products.php')
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                // Construct table rows from the fetched data
                const rows = data.data.map(product => `
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.quantity}</td>
                        <td>$${product.unit_price}</td>
                    </tr>
                `).join('');

                // Set the dynamic content
                content.innerHTML = `
                    <h2>Goods in Stock</h2>
                    <button id="showAddItemForm">Add Product</button>
                    <div id="formContainer" style="margin-top: 15px; display: none;">
                        <form id="addItemForm">
                            <label>Product Name: 
                                <input type="text" id="product_name" placeholder="Enter product name">
                            </label><br>
                            <label>Price: 
                                <input type="number" id="product_price" step="0.01" placeholder="Enter price">
                            </label><br>
                            <label>Quantity: 
                                <input type="number" id="product_quantity" placeholder="Enter quantity">
                            </label><br>
                            <button type="button" id="submitNewItem">Add Product</button>
                        </form>
                    </div>
                    <table border="1" cellspacing="0" cellpadding="5" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows}
                        </tbody>
                    </table>
                `;

                // Show form when clicking "Add Product" button
                document.getElementById('showAddItemForm').addEventListener('click', () => {
                    const formContainer = document.getElementById('formContainer');
                    formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
                });

                // Submit form to add a new product
                document.getElementById('submitNewItem').addEventListener('click', () => {
                    const productName = document.getElementById('product_name').value;
                    const productPrice = document.getElementById('product_price').value;
                    const productQuantity = document.getElementById('product_quantity').value;

                    // Input validation
                    if (!productName || !productPrice || !productQuantity) {
                        alert('Please fill in all fields.');
                        return;
                    }

                    // Send POST request to add_product.php
                    fetch('add_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_name=${encodeURIComponent(productName)}&product_price=${encodeURIComponent(productPrice)}&product_quantity=${encodeURIComponent(productQuantity)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            loadPage('inventory'); // Reload inventory to reflect the new data
                        } else {
                            alert(`Error: ${data.message}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                });

            } else {
                content.innerHTML = `<p>Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching inventory:', error);
            content.innerHTML = `<p>Error fetching inventory data. Please try again.</p>`;
        });
    break;


    case 'report':
    pageTitle.textContent = 'Reports';

    // Fetch report data from the server
    fetch('fetch_report.php')
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                // Display report data in a table format
                content.innerHTML = `
                    <h2>Sales Report</h2>
                    <table border="1" cellspacing="0" cellpadding="8" style="width: 50%; border-collapse: collapse; margin: auto;">
                        <thead>
                            <tr style="background-color: #f4f4f4; text-align: left;">
                                <th>Sales Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Daily Sales</strong></td>
                                <td>$${data.daily_sales}</td>
                            </tr>
                            <tr>
                                <td><strong>Monthly Sales</strong></td>
                                <td>$${data.monthly_sales}</td>
                            </tr>
                        </tbody>
                    </table>
                `;
            } else {
                content.innerHTML = `<p>Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching report:', error);
            content.innerHTML = `<p>Error fetching report data. Please try again.</p>`;
        });
    break;

    case 'manageRooms':
    pageTitle.textContent = 'Manage Rooms';

    // Fetch the PHP-generated room management page
    fetch('room_management.php')
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;

            // Event delegation for dynamic forms
            content.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Prevent full-page reload

                    const formData = new FormData(this);
                    fetch('room_management.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text()) // Expect HTML or updated content
                    .then(updatedHtml => {
                        content.innerHTML = updatedHtml; // Replace content dynamically
                        attachEventListeners(); // Re-attach event listeners
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            attachEventListeners(); // Attach event listeners for price input toggle
        })
        .catch(error => {
            console.error('Error loading room management page:', error);
            content.innerHTML = '<p>Error loading Manage Rooms. Please try again later.</p>';
        });

    function attachEventListeners() {
        // Ensure dynamic price input toggle works
        const selectElements = content.querySelectorAll('select[name="status"]');
        selectElements.forEach(select => {
            select.addEventListener('change', function () {
                const priceInput = this.nextElementSibling;
                if (this.value === 'booked') {
                    priceInput.disabled = false;
                } else {
                    priceInput.disabled = true;
                    priceInput.value = ''; // Clear price when status is vacant
                }
            });
        });
    }
    break;
                    default:
                        content.innerHTML = '<p>Page not found.</p>';
                }

                // Fade in content
                content.style.opacity = '1';
            }, 300);
        }
    </script>
    <script src="user.js"></script>
</body>
</html>
