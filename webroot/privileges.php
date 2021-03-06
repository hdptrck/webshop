<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/rootChecker.php");

// Declare var
$users = [];
$roles = [];

// Get all users from the database
$stmt = "SELECT * FROM webshopuser WHERE idWebshopUser <> " . $_SESSION['userId'] . ";";
if (!$result = $mysqli->query($stmt)) {
    echo "Oops! Something went wrong. Please try again later.";
    return false;
}

// Fetch result
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$result->free();

// Get all roles from the database
$stmt = "SELECT * FROM role;";
if (!$result = $mysqli->query($stmt)) {
    echo "Oops! Something went wrong. Please try again later.";
    return false;
}

// Fetch result
while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}

$result->free();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["message"])) {
        switch ($_GET["message"]) {
            case "roleError":
                $message = "Die Rolle des Benutzers konnte nicht angepasst werden";
                $noteClass = "note-danger";
                break;
            case "roleSuccess":
                $message = "Die Rolle des Benutzer wurde erfolgreich angepasst";
                $noteClass = "note-success";
                break;
            case "activateError":
                $message = "Der Benutzer konnte nicht angepasst werden";
                $noteClass = "note-danger";
                break;
            case "activateSuccess":
                $message = "Der Benutzer wurde erfolgreich angepasst";
                $noteClass = "note-success";
                break;
        }
    }
}

// Include header
$siteName = "Berechtigungen";
include("./includes/header.inc.php");

// Display message
if (isset($message)) {
    echo '<div id="message" class="note ' . $noteClass . ' mb-4"><p>' . $message . '</p></div>';
}

?>
<div class="row fadeIn">
    <?php
    // If users exist
    if (count($users)) {
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Name</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Rolle</th>
                    <th scope="col">Aktiv</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Create element for each user
                $rowNumber = 1;
                foreach ($users as $user) {
                    // Create tabele row for each item
                ?>
                    <tr>
                        <th scope="row"><?php echo  $rowNumber; ?></th>
                        <td><?php echo  $user['firstname']; ?></td>
                        <td><?php echo  $user['lastname']; ?></td>
                        <td><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
                        <td>
                            <!-- Select for user role -->
                            <select class="user-role-select" data-user-id="<?php echo $user["idWebShopUser"]; ?>">
                                <?php
                                foreach ($roles as $role) {
                                    echo '<option value="' . $role["idRole"] . '"';
                                    if ($role["idRole"] == $user["role_idRole"]) {
                                        echo " selected";
                                    }
                                    echo '>' . $role["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input user-active-checkbox" type="checkbox" value="<?php echo $user["active"]; ?>" data-user-id="<?php echo $user["idWebShopUser"]; ?>" <?php echo ($user["active"] == 1) ? 'checked' : ''; ?> />
                            </div>
                        </td>
                    </tr>
                <?php
                    $rowNumber++;
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
        echo '<span class="text-warning">Keine zusätzlichen Benutzer vorhanden</span>';
    }
    ?>
</div>

<script>
    //message fadeOut
    setTimeout(function() {
        document.getElementById("message").style.opacity = '0';
    }, 1);
    setTimeout(function() {
        document.getElementById("message").remove();
    }, 6001);

    // Get all user role select -events
    const userRoleSelectList = document.querySelectorAll('.user-role-select');
    const userActiveCheckboxList = document.querySelectorAll('.user-active-checkbox');

    // Changes the user role
    const roleAction = event => {
        const clickedItem = event.target;

        // Create GET Request
        fetch('privilegesHandler.php?userId=' + clickedItem.dataset.userId + '&role=' + clickedItem.value)
            .then(res => {
                return res.json();
            }).then(res => {
                if (res.code != 200) {
                    window.location = "privileges.php?message=roleError";
                } else {
                    window.location = "privileges.php?message=roleSuccess";
                }
            });
    };

    // Activates or deactivates users
    const activeAction = event => {
        const clickedItem = event.target;

        // Change active state for db update
        let active;
        if (clickedItem.value == 1) {
            active = 0;
        } else {
            active = 1;
        }

        console.log("active", active);

        // Create GET Request
        fetch('privilegesHandler.php?userId=' + clickedItem.dataset.userId + '&active=' + active)
            .then(res => {
                return res.json();
            }).then(res => {
                if (res.code != 200) {
                    window.location = "privileges.php?message=activateError";
                } else {
                    window.location = "privileges.php?message=activateSuccess";
                }
            });
    };

    // Adds event listeners
    userRoleSelectList.forEach(element => {
        element.addEventListener('change', roleAction);
    });

    userActiveCheckboxList.forEach(element => {
        element.addEventListener('change', activeAction);
    });
</script>

<?php
include("./includes/footer.inc.php");
?>