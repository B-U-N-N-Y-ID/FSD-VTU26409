<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Free</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/navhead.css">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            background:linear-gradient(135deg,#f1f5f9,#e2e8f0);
            min-height:100vh;
        }

        /* Wrapper to center only content */
        .page-wrapper{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:calc(100vh - 140px);
            padding:40px 20px;
        }

        .main-section{
            text-align:center;
            max-width:1000px;
            width:100%;
        }

        .main-section h1{
            font-size:34px;
            font-weight:700;
            margin-bottom:10px;
            color:#0f172a;
        }

        .main-section span{
            color:#64748b;
            font-size:16px;
        }

        .card-wrapper{
            margin-top:50px;
            display:flex;
            gap:40px;
            justify-content:center;
            flex-wrap:wrap;
        }

        .card{
            background:white;
            width:400px;
            padding:40px 30px;
            border-radius:18px;
            box-shadow:0 15px 40px rgba(0,0,0,0.08);
            transition:0.4s;
        }

        .card:hover{
            transform:translateY(-10px);
            box-shadow:0 25px 60px rgba(0,0,0,0.15);
        }

        .card h3{
            font-size:20px;
            margin-bottom:15px;
            color:#0f172a;
        }

        .card hr{
            border:none;
            height:1px;
            background:#e2e8f0;
            margin:15px 0 20px;
        }

        .card p{
            font-size:14px;
            color:#475569;
            line-height:1.6;
            margin-bottom:30px;
        }

        .card button{
            background:linear-gradient(135deg,#0f172a,#1e293b);
            border:none;
            padding:12px 25px;
            border-radius:12px;
            cursor:pointer;
            transition:0.3s;
        }

        .card button:hover{
            background:linear-gradient(135deg,#1e293b,#334155);
            transform:translateY(-3px);
        }

        .card button a{
            text-decoration:none;
            color:white;
            font-weight:500;
            font-size:14px;
        }

        @media(max-width:900px){
            .card-wrapper{
                flex-direction:column;
                align-items:center;
            }
        }
    </style>
</head>

<body>

<?php include 'header.php'; ?>

<div class="page-wrapper">
    <div class="main-section">

        <h1>Register free for online testing</h1>
        <span>Secure, Reliable, Professional.</span>

        <div class="card-wrapper">

            <div class="card">
                <h3>Register To Take Test</h3>
                <hr>
                <p>
                    Register here if you have been instructed to take a Test by an instructor or administrator.
                </p>
                <button>
                    <a href="registerform.php">Register</a>
                </button>
            </div>

            <div class="card">
                <h3>Register To Create Test</h3>
                <hr>
                <p>
                    Register here if you want to create and manage online tests for students or employees.
                </p>
                <button>
                    <a href="registerformcreate.php">Register</a>
                </button>
            </div>

        </div>

    </div>
</div>

</body>
</html>