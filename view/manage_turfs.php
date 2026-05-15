<?php
session_start();
require_once '../config/db_config.php';

// Access Control - Ensures only managers can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: ../controller/login.php");
    exit();
}

$message = "";

// Logic to handle adding a new turf
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_turf'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = $_POST['price'];
    $size = $_POST['size'];

    // SQL to insert data into the turfs table
    $sql = "INSERT INTO turfs (name, location, price, size) VALUES ('$name', '$location', '$price', '$size')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Turf added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

// Fetch all registered turfs from the database
$result = $conn->query("SELECT * FROM turfs ORDER BY id DESC");

include '../includes/manager_header.php'; 
?>

<div style="display: flex; gap: 20px; flex-direction: column;">
    
    <div class="glass-card" style="padding: 25px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
        <h3 style="color: #2ecc71; margin-bottom: 20px; font-size: 22px;">➕ Add New Turf</h3>
        
        <?php if($message): ?>
            <p style="margin-bottom:15px; font-weight:bold; color:#2ecc71; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        
        <form method="POST" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; align-items: end;">
            
            <div class="input-group" style="margin:0; text-align: left;">
                <label style="color: #87CEEB; font-weight: bold; font-size: 16px; margin-bottom: 8px; display: block;">Turf Name</label>
                <input type="text" name="name" required placeholder="e.g. Arena 7" style="width: 100%; padding: 12px; border-radius: 10px; border: none; background: rgba(255,255,255,0.9);">
            </div>

            <div class="input-group" style="margin:0; text-align: left;">
                <label style="color: #87CEEB; font-weight: bold; font-size: 16px; margin-bottom: 8px; display: block;">Location</label>
                <input type="text" name="location" required placeholder="e.g. Uttara" style="width: 100%; padding: 12px; border-radius: 10px; border: none; background: rgba(255,255,255,0.9);">
            </div>

            <div class="input-group" style="margin:0; text-align: left;">
                <label style="color: #87CEEB; font-weight: bold; font-size: 16px; margin-bottom: 8px; display: block;">Price ($/hr)</label>
                <input type="number" name="price" required placeholder="30" style="width: 100%; padding: 12px; border-radius: 10px; border: none; background: rgba(255,255,255,0.9);">
            </div>

            <div class="input-group" style="margin:0; text-align: left;">
                <label style="color: #87CEEB; font-weight: bold; font-size: 16px; margin-bottom: 8px; display: block;">Size</label>
                <select name="size" style="width:100%; padding:12px; border-radius:10px; border:none; background:rgba(255,255,255,0.9); cursor: pointer;">
                    <option value="5-a-side">5-a-side</option>
                    <option value="7-a-side">7-a-side</option>
                    <option value="9-a-side">9-a-side</option>
                    <option value="11-a-side">11-a-side</option>
                </select>
            </div>

            <button type="submit" name="add_turf" class="btn" style="grid-column: span 4; margin-top: 15px; height: 45px; font-weight: bold; text-transform: uppercase;">
                Register Turf
            </button>
        </form>
    </div>

    <div class="glass-card" style="padding: 25px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
        <h3 style="color: #2ecc71; margin-bottom: 20px; font-size: 22px;">🏟️ Existing Turfs</h3>
        <table style="width: 100%; border-collapse: collapse; color: white; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid rgba(46, 204, 113, 0.5); color: #2ecc71;">
                    <th style="padding: 15px;">ID</th>
                    <th style="padding: 15px;">Name</th>
                    <th style="padding: 15px;">Location</th>
                    <th style="padding: 15px;">Size</th>
                    <th style="padding: 15px;">Price</th>
                    <th style="padding: 15px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 15px; opacity: 0.8;"><?php echo $row['id']; ?></td>
                            <td style="padding: 15px; font-weight: bold; color: #ffffff;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($row['location']); ?></td>
                            <td style="padding: 15px; font-style: italic;"><?php echo htmlspecialchars($row['size']); ?></td>
                            <td style="padding: 15px; color: #87CEEB; font-weight: bold;">$<?php echo number_format($row['price'], 2); ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="../controller/delete_turf.php?id=<?php echo $row['id']; ?>" 
                                   style="color: #e74c3c; text-decoration: none; font-weight: bold; border: 1px solid #e74c3c; padding: 5px 10px; border-radius: 5px; font-size: 12px;" 
                                   onclick="return confirm('Are you sure you want to delete this turf?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; opacity: 0.5; font-style: italic;">No turfs registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>