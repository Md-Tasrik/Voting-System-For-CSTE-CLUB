<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <?php
    // Ensure voters table has an email column
    $checkEmailCol = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'");
    if($checkEmailCol && $checkEmailCol->num_rows === 0){
      $conn->query("ALTER TABLE voters ADD COLUMN email varchar(100) NULL AFTER lastname");
    }
  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Voters List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Voters</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']." 
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']." 
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              <a href="download.php" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Import Data</a>
            </div>

            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Lastname</th>
                  <th>Firstname</th>
                  <th>Photo</th>
                  <th>Voters ID</th>
                  <th>Password</th>
                  <th>Email</th>
                  <th>Tools</th>
                  <th>Send Voting Link</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM voters";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      echo "
                        <tr>
                          <td>".$row['lastname']."</td>
                          <td>".$row['firstname']."</td>
                          <td>
                            <img src='".$image."' width='30px' height='30px'>
                            <a href='#edit_photo' data-toggle='modal' class='pull-right photo' data-id='".$row['id']."'><span class='fa fa-edit'></span></a>
                          </td>
                          <td>".$row['voters_id']."</td>
                          <td>".$row['vpass']."</td>
                          <td>".(isset($row['email']) ? $row['email'] : '')."</td>
                          <td>
                            <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
                          </td>
                          <td>
                            <button class='btn btn-primary btn-sm send-voting-link' data-id='".$row['id']."' data-toggle='modal' data-target='#sendVotingLinkModal'>
                              <i class='fa fa-envelope'></i> Send Voting Link
                            </button>
                          </td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/voters_modal.php'; ?>
</div>

<!-- Modal for Sending Voting Link -->
<div class="modal fade" id="sendVotingLinkModal" tabindex="-1" role="dialog" aria-labelledby="sendVotingLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendVotingLinkModalLabel">Send Voting Link</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="sendVotingLinkForm">
          <input type="hidden" id="voterId" name="voterId">
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="emailsubject">Subject</label>
            <input type="text" class="form-control" id="emailsubject" name="emailsubject" value="CSTE Club Election - Vote Now" required>
          </div>
          <div class="form-group">
            <label for="emailBody">Body</label>
            <textarea class="form-control" id="emailBody" name="emailBody" rows="6" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Email</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  $(document).on('click', '.send-voting-link', function() {
    var voterId = $(this).data('id');
    $('#voterId').val(voterId);
    // Prefill from server
    $.ajax({
      type: 'POST',
      url: 'voters_row.php',
      data: { id: voterId },
      dataType: 'json',
      success: function(response){
        if(response){
          $('#email').val(response.email || '');
          // Provide a helpful default body; server will replace [VOTING_LINK]
          var name = (response.firstname ? response.firstname + ' ' : '') + (response.lastname ? response.lastname : '');
          $('#emailBody').val('Hello ' + name + ',\n\nYour voting credentials:\n- Voter ID: [VOTER_ID]\n- Password: [PASSWORD]\n\nPlease cast your vote using the secure link below:\n[VOTING_LINK]\n\nThank you.');
        }
      }
    });
  });

  $('#sendVotingLinkForm').submit(function(e) {
    e.preventDefault(); // Prevent default form submission

    var formData = $(this).serialize(); // Get the form data

    $.ajax({
        type: 'POST',
        url: 'send_voting_link.php', // PHP script to send the email
        data: formData,
        success: function(response) {
            alert(response); // Success message
            $('#sendVotingLinkModal').modal('hide'); // Close the modal
        },
        error: function() {
            alert('Error sending email.');
        }
    });
  });
});
</script>

</body>
</html>
