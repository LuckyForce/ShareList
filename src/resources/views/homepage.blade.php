@include('header', ['title' => 'ShareList - Home', 'url' => 'home'])
Hier wird ein heftiges Projekt entstehen.
<?php
$results = DB::select('select * from l_u_users', array(1));
foreach ($results as $result) {
    echo $result->l_u_email;
}
?>
@include('footer')