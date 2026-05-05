# 📰 Prime News – E-News Web Platform

## 📌 Project Overview

Prime News is a dynamic web-based news platform that allows users to browse, search, and interact with news articles across multiple categories.

The system is designed with role-based access for Users, Journalists, and Admin, enabling structured content management, secure authentication, and efficient news publishing workflow.

---

## 🚀 Features

### 👤 User Functionality
- User can register and login securely  
- OTP verification via email during registration (using PHPMailer)  
- User lands on homepage after login  
- View news from multiple categories  
- Select category to filter news  
- View news with images and videos  
- Enable dark mode  
- Inside news:
  - Listen to article  
  - Save news  
  - Share news  
  - Translate article  
- Access saved news page and remove saved news  

---

### 📝 Journalist Functionality
- Journalist can write news articles  
- Upload images and videos  
- Submit news to admin for approval  

---

### 🔐 Admin Functionality
- Admin can publish news to website  
- Add, update, edit, and delete news  
- Manage categories (add, update, delete)  
- View liked news in a dedicated page  
- Generate PDF report of liked news using TCPDF  

---

## 🔐 Authentication & Access Control

- OTP-based email verification during user registration (PHPMailer)  
- Only logged-in users can access advanced features:
  - Listen to article  
  - Save news  
  - Share news  
  - Translate article  

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **Email Service:** PHPMailer  
- **PDF Generation:** TCPDF  

---

## 📂 Project Structure

prime-news-web-app/
│
├── doc/                 # Complete project documentation  
├── screenshots/         # UI screenshots  
│
├── src/  
│   ├── uploads/         # Uploaded images & videos (by journalist)  
│   ├── images/          # Static website images  
│   ├── database/        # SQL file  
│   └── code/            # All application code (PHP, HTML, CSS, JS)  
│
└── README.md  

---

## ⚙️ Installation & Setup

### 1. Clone Repository

git clone https://github.com/yourusername/prime-news-web-app.git


### 2. Move Project to Server
- Place project folder inside:
  - `htdocs` (XAMPP)  
  - `www` (WAMP)  

### 3. Setup Database
- Open phpMyAdmin  
- Create database (e.g., `prime_news`)  
- Import SQL file from:
  - src/database/


### 4. Configure Database Connection
- Update database credentials in backend config file:
  - hostname  
  - username  
  - password  
  - database name  

### 5. Run Project
- Start Apache & MySQL  
- Open browser:

http://localhost/prime-news-web-app


---

## 🔑 Admin Access (Demo)

Username: admin  
Password: admin123  

---

## 🎥 Demo

👉 Add your video link here  

---

## 📸 Screenshots

(Add homepage, admin panel, journalist panel, features)

---

## 💡 Key Learnings

- Full-stack web development  
- Role-based system design  
- Email authentication using PHPMailer  
- Secure feature access for logged-in users  
- File handling (image/video uploads)  
- PDF generation using TCPDF  
- Building interactive and dynamic web applications  

---

## 🔮 Future Improvements

- Comment and like system for users  
- REST API integration  
- Mobile responsiveness improvements  
- Advanced search and filtering  
- Notification system  

---

## 👨‍💻 Author

Tirth R. Modi  
