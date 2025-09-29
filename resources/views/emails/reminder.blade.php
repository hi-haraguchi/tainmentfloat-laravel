<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
    .container {
      max-width: 600px;
      margin: 30px auto;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 30px;
      text-align: center;
    }
    .logo {
      margin-bottom: 20px;
    }
    .title {
      font-size: 18px;
      margin-bottom: 16px;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-size: 16px;
    }
    .footer {
      margin-top: 30px;
      font-size: 12px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="{{ config('app.url') }}/tf-rogo-p22.png" alt="エンタメフロート ロゴ" width="200">
    </div>

    <!-- <p class="title">{{ $user['email'] }} さん</p> -->
    @if($genreName)
      <p>「{{ $genreName }}」のジャンルについて、<Br>
      前回から<strong>{{ $days }}日</strong> がたちました。</p>
    @else
      <p>前回から <strong>{{ $days }}日</strong> がたちました。</p>
    @endif

    @if($lastTitle) <p>前回は「<strong>{{ $lastTitle->title }}</strong>」を<Br>楽しまれたようです。</p> @endif

    <p>もしお時間や体力がゆるすのであれば<Br>新しいエンタメを楽しまれてください！</p>

    <a href="{{ config('app.url') }}/" class="btn">アプリを開く</a>

    <div class="footer" style="margin-top:30px; font-size:12px; color:#666; text-align:left;">
      <p style="margin:0;">※このメールは自動送信されています。</p>
      <p style="margin:0;">※リマインドメールを止める場合は、ログイン後に（その他）メニューのリマインド設定から変更してください</p>
    </div>
  </div>
</body>
</html>

