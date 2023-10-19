<?php 
$user = $resetPasswordData['user'];
$gt_title = 'AUDIOFREE — сброс пароля';
$gt_contents = [
    ['content' => 'Вы получили это письмо, так как был запрошен сброс пароля для вашего профиля'],
    ['content' => 'Ваш новый пароль ниже. Рекомендуем удалить письмо и изменить пароль в настройках профиля'],
    [
        'content' => $resetPasswordData['newPassword'], 
        'code-like' => true
    ]
]; ?>

@include('email.GeneralTemplate')

<?php 