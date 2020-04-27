<!-- Modale per Chat -->
<div class="modal fade" id="addChatModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add a New Chat</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Lista amici, (tipo di chat, titolo gruppo/canale) -->
                <div class="alert alert-primary" role="alert">
                    Seleziona gli amici:
                </div>
                <select id="selectChat" multiple="multiple">
                    <?php
                    foreach ($detailsFriends as $friend) {
                        echo "<option value='" . $friend->getUserId() . "'>" . $friend->getUsername() . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success">Add</button>
            </div>
        </div>
    </div>
</div>