    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <!-- <h1 class="h4 mb-4 text-gray-800"><?= $title; ?></h1> -->
        <?= form_error('menu', '<div class="alert alert-danger" roles="alert">', '</div>') ?>
        <?= $this->session->flashdata('message'); ?>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Job</th>
                                <th>ID</th>
                                <th>State</th>
                                <th>Policy name</th>
                                <th>Schedule</th>
                                <th>Elapsed time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Job</th>
                                <th>ID</th>
                                <th>State</th>
                                <th>Policy name</th>
                                <th>Schedule</th>
                                <th>Elapsed time</th>
                                <th>Status</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php $i = 0; ?>
                            <?php $a = 1; ?>
                            <?php foreach ($result['data'] as $r) : ?>
                                <tr>
                                    <th scope="row"><?= $a; ?></th>
                                    <td>
                                        <?php if ($result['data'][$i]['attributes']['jobType'] == "DBBACKUP") {
                                            echo "Catalog Backup";
                                        } elseif ($result['data'][$i]['attributes']['jobType'] == "IMAGEDELETE") {
                                            echo "Image Cleanup";
                                        } else {
                                            echo $result['data'][$i]['attributes']['jobType'];
                                        }
                                        ?>
                                    </td>
                                    <td><?= $result['data'][$i]['attributes']['jobId']; ?></td>
                                    <td><?= $result['data'][$i]['attributes']['state']; ?></td>
                                    <td><?= $result['data'][$i]['attributes']['policyName']; ?></td>
                                    <td><?= $result['data'][$i]['attributes']['scheduleName']; ?></td>
                                    <td><?= $result['data'][$i]['attributes']['elapsedTime']; ?></td>
                                    <td>
                                        <?php if ($result['data'][$i]['attributes']['state'] == "DONE") {
                                            echo $result['data'][$i]['attributes']['status'];
                                        } else {
                                            echo "Running";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                                <?php $a++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    </div>

    <!-- End of Main Content -->

    <!-- Modal -->
    <div class="modal fade" id="newRoleModal" tabindex="-1" role="dialog" aria-labelledby="newRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newRoleModalLabel">Add New Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php base_url('admin/role'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="role" name="role" placeholder="Role Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>