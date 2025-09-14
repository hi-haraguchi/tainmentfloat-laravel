<p>{{ $user['email'] }}さん、前回の登録から {{ $days }}日 がたちました。</p>

@if($lastTitle)
<p>前回は「<strong>{{ $lastTitle->title }}</strong>」を楽しまれたようです。</p>
@endif

<p>そろそろ、新しいエンタメを楽しみませんか？</p>

<p>
  <a href="{{ config('app.url') }}/" style="color:#fff;background:#007bff;padding:10px 20px;border-radius:4px;text-decoration:none;">
    アプリを開く
  </a>
</p>

<p style="font-size:12px;color:#666;">※このメールは自動送信されています。</p>
