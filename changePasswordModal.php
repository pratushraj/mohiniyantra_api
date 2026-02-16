<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordLabel">Change Your Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label for="" class="form-label">Old Password</label>
            <input type="password" class="form-control" id="oldPasssword">
          </div>
          <div class="col-md-12">
            <label for="" class="form-label">New Password</label>
            <input type="password" id="newPassword" class="form-control">
          </div>
          <div class="col-md-12">
            <label for="" class="form-label">Confirm New Password</label>
            <input type="password" id="cnfNewPassword" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="closeChangePAssword" class="btn btn-primary">Modify</button>
      </div>
    </div>
  </div>
</div>

<script type="module" src="./urls.js"></script>

<script type="module">
  import {
    CHANGE_PASSWORD_URL
  } from './urls.js';

  // close a modal dynamically

  const old_pwd = document.getElementById('oldPasssword')
  const new_pwd = document.getElementById('newPassword')
  const cnf_pwd = document.getElementById('cnfNewPassword')

  document.getElementById('closeChangePAssword').addEventListener('click', async function() {
    if (old_pwd.value == "" || new_pwd.value == "" || cnf_pwd.value == "") {
      alert("All fields are mandatory");
    } else if (new_pwd.value !== cnf_pwd.value) {
      alert("New Password & Confirmed Password does not matched ");
    } else if (new_pwd.value === cnf_pwd.value) {
      try {
        const changePasswordObj = {
          old_password: old_pwd.value,
          new_password: new_pwd.value,
          user_id: JSON.parse(localStorage.getItem('loginInfo')).id
        }
        const result = await fetch(CHANGE_PASSWORD_URL, {
          mode: 'no-cors',
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(changePasswordObj)
        });

        const res = await result.json();
        if (result.ok) {
          document.getElementById('oldPasssword').value = '';
          document.getElementById('newPassword').value = '';
          document.getElementById('cnfNewPassword').value = '';
          alert("Password Changed Successfully")
          var modalElement = document.getElementById('changePasswordModal');
          var modalInstance = bootstrap.Modal.getInstance(modalElement);
          if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
          }
          modalInstance.hide();

        } else {
          alert(res.msg)
        }

      } catch (error) {
        alert(error.msg)
      }
    }
  })
</script>