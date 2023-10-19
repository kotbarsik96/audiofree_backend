<?php 
$user = $userVerificationEmailData['user'];
$gt_title = 'AUDIOFREE — подтверждение почтового адреса';
$gt_contents = [
    ['content' => 'Вы получили это письмо, так как было запрошено подтверждение адреса электронной почты'],
    ['content' => 'Чтобы подтвердить адрес, введите этот код в окне, где запрашивали письмо:'],
    [
        'content' => $userVerificationEmailData['code'], 
        'code-like' => true
    ]
]; ?>

@include('email.GeneralTemplate')

<?php 