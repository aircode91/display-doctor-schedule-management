<div class="card">
    <div class="card-body">
        <form id="f_order" class="form-horizontal" method="post" enctype="multipart/form-data">
            <!-- Modal content-->
            <div class="form-group row">
                <input type="hidden" class="form-control input" name="id" value="">
                <label class="col-3 control-label">Pasien</label>
                <div class="col-8">
                    <div id="btn-pasien">
                        <a class="btn btn-sm btn-primary btn-square" data-toggle="modal" href="#m_pasien">Pilih Pasien</a>
                    </div>
                    <div id="list-pasien"></div>
                </div>

            </div>
            <div class=" form-group row">
                <label class="col-3 control-label">Menu 1</label>
                <div class="col-8">
                    <div id="menu-1">
                        <button class="btn btn-sm btn-primary btn-square btn-menu-1" type="button"> Pilih Menu</button>
                    </div>
                    <div id="list-menu-1"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">Menu 2</label>
                <div class="col-8">
                    <div class="btn-menu-2">
                        <button class="btn btn-sm btn-primary btn-square btn-menu-2" type="button"> Pilih Menu</button>
                    </div>
                    <div id="list-menu-2"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">Menu 3</label>
                <div class="col-8">
                    <div class="btn-menu-3">
                        <button class="btn btn-sm btn-primary btn-square btn-menu-3" type="button"> Pilih Menu</button>
                    </div>
                    <div id="list-menu-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-square" id="b_order">Simpan</button>
                <button type="button" class="btn btn-danger btn-square" data-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="m_pasien" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Data Pasien</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" style="overflow:scroll;height:500px;">
                    <table class="table table-striped datatables-basic" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NO Rekam Medis</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Jenis Kelamin</th>
                                <th>Suku</th>
                                <th>Agama</th>
                                <th width="5%">Choose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($list_patients as $patient) {
                                ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $patient->no_rm; ?></td>
                                    <td><?= $patient->nama; ?></td>
                                    <td><?= $patient->tgl_lahir ?> </td>
                                    <td><?= $patient->alamat ?> </td>
                                    <td><?= $patient->sex == 'L' ? 'Laki- Laki' : 'Perempuan'; ?> </td>
                                    <td><?= $patient->suku ?> </td>
                                    <td><?= $patient->agama ?> </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-success btn-square t_pasien" title="Pilih" data-id="<?= $patient->id ?>" data-name="<?= $patient->nama ?>"><span class="fa fa-check"></span></button>

                                    </td>
                                </tr>
                            <?php
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-square btn-danger" data-dismiss="modal" aria-hidden="true">Tutup</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="v_menu" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="myModalLabel">List Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        foreach ($list_menus as $menu) {
                            ?>
                            <div class="col-4">
                                <div class="card text-center item">
                                    <a href="javascript:void();" data-id="<?= $menu->id ?>" data-name="<?= $menu->name ?>" class="t_item">
                                        <div class="card-body">
                                            <div class="text">
                                                <b><?= $menu->name; ?></b>
                                                <p><?= $menu->description; ?></p>

                                            </div>
                                            <img style="width:150px;height:150px" src="<?= base_url('assets/images/uploads/menus/') . $menu->image ?>" name="view-image" alt="image-files">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    .item:hover {
        opacity: 1;
        padding: 0px;
        box-shadow: 1px 1px 5px #47bac1;
    }

    .text {
        text-decoration: none;
    }
</style>

<script>
    $(document).ready(function() {
        $('.t_pasien').click(function() {
            var id = $(this).attr("data-id");
            var nama = $(this).attr("data-name");
            $('#m_pasien').modal('hide');
            $('#btn-pasien').hide();
            $('#list-pasien').html('<div id="list-pasien-current"><input type="hidden" name="pasien" value="' + id + '" /> <b>' + nama + '</b><button type="button" class="btn btn-danger btn-sm ml-5" onclick="hapusData()" title="Delete patient"> <i class="fa fa-minus-circle" aria-hidden="true"></i></button></div>');
        });


        // var btn = "btn-menu-";
        // var i;
        // for (i = 1; i <= 3; i++) {
        //     var btnMenu = btn + i;

        //     $('.' + btnMenu + '').click(function() {

        //         console.log(btnMenu);
        //     });
        //     // btn += i;
        // }

        $.each([1, 2, 3], function(index, value) {
            var btnMenu = ".btn-menu-" + value;
            $(btnMenu).click(function() {
                var id = $(this).attr("data-id");
                var nama = $(this).attr("data-name");
                $('#list-menu-' + value).html('');
                // $("#v_menu").modal('show');
                // $('.t_item').attr("data-src", value);
                // console.log(btnMenu);
            });
        });

        // $.each([1, 2, 3], function(index, value) {
        //     var btnHapus = "#bhapusMenu" + value;
        //     $(btnHapus).click(function() {
        //         var id = $(this).attr("data-id");
        //         var nama = $(this).attr("data-name");
        //         $("#v_menu").modal('show');
        //         $('.t_item').attr("data-src", value);
        //         // console.log(btnMenu);
        //     });
        // });

        $('.t_item').click(function() {
            var id = $(this).attr("data-id");
            var nama = $(this).attr("data-name");
            var num = $(this).attr("data-src");
            $('#v_menu').modal('hide');
            $('.btn-menu-' + num).hide();
            $('#list-menu-' + num).html('<div id="list-pasien-current"><input type="hidden" name="menu-' + num + '" value="' + id + '" /> <b>' + nama + '</b><button type="button" class="btn btn-danger btn-sm ml-5 " id="hapusMenu' + num + '" title="Delete Menu"> <i class="fa fa-minus-circle" aria-hidden="true"></i></button></div>');
        });

    });

    function hapusData() {
        // divPasien = document.getElementById('list-pasien-current');
        document.getElementById("list-pasien").innerHTML = "";
        document.getElementById("btn-pasien").style.display = "block";
    }
</script>