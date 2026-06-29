<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 240px;
        height: 100%;
        background: #294fc2;
        color: white;
        padding-top: 25px;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 35px;
        color: white;
    }

    .sidebar ul {
        list-style: none;
    }

    .sidebar ul li {
        margin: 8px 15px;
    }

    .sidebar ul li a {
        display: block;
        padding: 12px 18px;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: .3s;
    }

    .sidebar ul li a:hover {
        background: #ffffff22;
    }

    .sidebar ul li a.active {
        background: white;
        color: #0057d9;
        font-weight: bold;
    }

    .content {
        margin-left: 250px;
        padding: 30px;
    }
</style>

<div class="sidebar">

    <h2>KTM eDOIS</h2>

    <ul>

        <li>
            <a class="<?= ($current == "submit_do.php") ? "active" : ""; ?>"
                href="../Module2/submit_do.php">
                📄 Submit DO
            </a>
        </li>

        <li>
            <a class="<?= ($current == "do_history.php") ? "active" : ""; ?>"
                href="../Module2/do_history.php">
                📋 DO History
            </a>
        </li>

    </ul>

</div>