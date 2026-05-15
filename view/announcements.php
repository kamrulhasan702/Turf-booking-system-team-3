<?php
session_start();
require_once '../config/db_config.php';

// Access Control - Ensures only managers can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: ../controller/login.php");
    exit();
}

// Logic to fetch data if the manager is editing an existing announcement
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id = (int)$_GET['edit_id'];
    $res = $conn->query("SELECT * FROM announcements WHERE id = $id");
    $edit_data = $res->fetch_assoc();
}

// Fetch all announcements for the management table
$result = $conn->query("SELECT * FROM announcements ORDER BY post_date DESC");

include '../includes/manager_header.php';
?>

<div style="display: flex; flex-direction: column; gap: 30px;">
    
    <div class="glass-card" style="padding: 25px;">
        <h3 style="color: #2ecc71; margin-bottom: 20px;">
            <?php echo $edit_data ? "✏️ Edit Announcement" : "📢 Post New Announcement"; ?>
        </h3>
        
        <form action="../controller/announcements.php" method="POST">
            <input type="hidden" name="announcement_id" value="<?php echo $edit_data['id'] ?? ''; ?>">
            
            <div class="input-group" style="margin-bottom: 20px; text-align: left;">
                <label style="color: #87CEEB; font-weight: bold; font-size: 18px; margin-bottom: 8px; display: block;">Title</label>
                <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" 
                       style="color: #87CEEB; font-weight: bold; font-size: 18px; width: 100%; padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.9);" required>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 15px;">
                <div class="input-group" style="flex: 1; text-align: left;">
                    <label style="color: #87CEEB; font-weight: bold; font-size: 18px; margin-bottom: 8px; display: block;">Discount (%)</label>
                    <input type="number" name="discount" value="<?php echo $edit_data['discount'] ?? '0'; ?>" 
                           style="color: #87CEEB; font-weight: bold; font-size: 18px; width: 100%; padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.9);">
                </div>

                <div class="input-group" style="flex: 2; text-align: left;">
                    <label style="color: #87CEEB; font-weight: bold; font-size: 18px; margin-bottom: 8px; display: block;">Message</label>
                    <input type="text" name="message" value="<?php echo $edit_data['message'] ?? ''; ?>" 
                           style="color: #87CEEB; font-weight: bold; font-size: 18px; width: 100%; padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.9);" required>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" class="btn" style="margin: 0; flex: 2; font-weight: bold; height: 50px;">
                    <?php echo $edit_data ? "Update Post" : "Broadcast Offer"; ?>
                </button>
                <?php if($edit_data): ?>
                    <a href="announcements.php" style="flex: 1;">
                        <button type="button" class="btn" style="margin: 0; background: #95a5a6; font-weight: bold; width: 100%; height: 50px;">Cancel</button>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="glass-card" style="padding: 25px;">
        <h3 style="color: #2ecc71; margin-bottom: 20px;">📜 Manage Announcements</h3>
        <table style="width: 100%; border-collapse: collapse; color: white; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2); color: #2ecc71;">
                    <th style="padding: 12px;">Date</th>
                    <th style="padding: 12px;">Title</th>
                    <th style="padding: 12px;">Discount</th>
                    <th style="padding: 12px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 12px; font-size: 13px;"><?php echo $row['post_date']; ?></td>
                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td style="padding: 12px; font-weight: bold; color: #87CEEB;"><?php echo $row['discount']; ?>%</td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="announcements.php?edit_id=<?php echo $row['id']; ?>" style="color: #3498db; text-decoration: none; margin-right: 15px; font-weight: bold;">✏️ Edit</a>
                        <a href="../controller/announcements.php?delete_id=<?php echo $row['id']; ?>" 
                           style="color: #e74c3c; text-decoration: none; font-weight: bold;" 
                           onclick="return confirm('Delete this announcement forever?')">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; opacity: 0.5;">No announcements found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>