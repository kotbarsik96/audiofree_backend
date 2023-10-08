<table style="width: 100%;">
    <tbody style="font-weight: 500; font-size: 20px; line-height: 1.25; text-align: center;">
        <tr>
            <td
                style="font-size: 24px; font-weight: 700; color: #fff; background-color: #8b75c8; padding: 30px 30px 30px 30px; margin-bottom: 20px;">
                <h1>AUDIOFREE — подтверждение почтового адреса</h1>
            </td>
        </tr>
        <tr>
            <td>
                <h2 style="font-size: 22px; margin-bottom: 20px; font-weight: 600;">
                    Приветствуем,
                    <?=$userVerificationEmailData['user']->name ?>!
                </h2>
            </td>
        </tr>
        <tr>
            <td>
                Вы получили это письмо, так как было запрошено подтверждение адреса электронной почты
            </td>
        </tr>
        <tr>
            <td>
                Чтобы подтвердить адрес, введите этот код в окне, где запрашивали письмо:
            </td>
        </tr>
        <tr>
            <td>
                <div style="font-weight: 700; font-size: 30px; margin-top: 15px;">
                    <?= $userVerificationEmailData['code'] ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>