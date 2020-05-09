<!-- Modale per Chat -->
<div class="modal fade" id="addChatModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add one or more users to the chat</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="formAddUser">
                <div class="modal-body">
                    <?php
                    if (count($detailsFriends) == 0) {
                        echo "<p>Hai già aggiunto tutti i tuoi amici!</p>";
                    } else {
                    ?>
                        <select id="selectUsers" multiple="multiple" name='users[]'>
                            <?php
                            foreach ($detailsFriends as $friend) {
                                $src = $friend->getPathProfilePicture();
                                echo "<option src='" . $src . "' value='" . $friend->getUserId() . "'>" . $friend->getUsername() . "</option>";
                            }
                            ?>
                        </select>

                </div>
                <div class="modal-footer">
                    <button id="btnAdd" type="submit" class="btn btn-success">Add</button>
                </div>
            <?php
                        echo '</div>';
                    }
            ?>
            </form>
        </div>
    </div>
</div>