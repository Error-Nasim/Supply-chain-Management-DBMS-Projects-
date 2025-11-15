<div align="center">
   <img src="https://img.icons8.com/color/96/food.png" alt="MSN FOOD Logo"/>
  
   # 🥇 **MSN FOOD Supply Chain Management System (SCM)** 🥇
  
   <h2 style="color:#2ecc71;">Welcome to the Ultimate Food Supply Chain Solution!</h2>
   <h3 style="color:#e67e22;">Fast. Secure. Joss. 🚀</h3>
</div>

---

## 🔥 Quick Start & Default Credentials

> **Default Admin Login:**
>
> - **Username:** `admin`
> - **Password:** `admin123`

> ⚡ **Change the password after first login for security!**

---

## Overview

MSN FOOD SCM is a web-based supply chain management system built with PHP and MySQL/MariaDB. It streamlines product, order, and inventory management for manufacturers, retailers, and administrators. The system is designed for food distribution businesses and supports multi-role access, order workflows, and inventory tracking.

## ✨ Features

- **Admin Panel**: Manage products, manufacturers, retailers, units, and areas
- **Manufacturer Portal**: Manage stock, view orders, generate invoices
- **Retailer Portal**: Place orders, view invoices, track order status
- **Product Catalog**: Centralized product management
- **Inventory Tracking**: Per-manufacturer stock management
- **Order & Invoice Workflow**: End-to-end order processing and billing
- **Role-based Access**: Secure login for admin, manufacturer, retailer

## 🛠️ Tech Stack

- **Backend**: PHP (procedural)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript (jQuery, jQuery UI)
- **Web Server**: Apache (XAMPP recommended for local setup)

## 📁 Project Structure

```text
scm/
├── admin/           # Admin dashboard and management
├── manufacturer/    # Manufacturer dashboard
├── retailer/        # Retailer dashboard
├── includes/        # Shared includes (config, header, footer, styles)
├── images/          # UI images
├── backup/          # Backup files and documentation
├── index.php        # Main login page
├── logout.php       # Logout handler
├── config.php       # Main config
└── README.md        # Project documentation
```

## 🚀 Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/msn-food-scm.git
   cd msn-food-scm
   ```
2. **Import the database**
   - Open phpMyAdmin or use MySQL CLI
   - Create a database named `scms`
   - Import the provided `scms.sql` file:
     ```bash
     mysql -u root -p scms < scms.sql
     ```
3. **Configure database connection**
   - Edit `includes/config.php` if your DB credentials differ from default (`root`/no password)
4. **Run the project locally**
   - Place the project folder in your web server root (e.g., `/opt/lampp/htdocs/` for XAMPP)
   - Start Apache and MySQL (via XAMPP or systemctl)
   - Access the app at `http://localhost/scm/`

## 🎮 Usage

- **Admin Login**: Manage products, units, areas, manufacturers, retailers
- **Manufacturer Login**: Manage stock, view orders, generate invoices
- **Retailer Login**: Place orders, view invoices
- **Logout**: Use the logout button in the dashboard

## 🔒 Security Notes

- Change default DB credentials in `includes/config.php`
- Use strong passwords for all user accounts
- For production, move to password hashing (bcrypt) and HTTPS

## 🤝 Contributing

Pull requests and suggestions are welcome! Please fork the repo and submit a PR.

## 📜 License

This project is licensed under the MSN License.

## 📬 Contact

For questions or support, contact the project maintainer at [manasim1213@gmail.com].

---

<div align="center">
   <img src="https://img.icons8.com/color/96/party-baloons.png" alt="Party"/>
  
   <h2 style="color:#e74c3c;">Made with ❤️ for the food supply chain. <br>Be With MSN, Be joss, Be awesome!</h2>
</div>

---
