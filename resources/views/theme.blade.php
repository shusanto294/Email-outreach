<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Outreach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
      .active>.page-link, .page-link.active {
          z-index: 3;
          color: #fff;
          background-color: #333;
          border-color: #333;
      }
      .page-link {
        color: #333;
        text-decoration: none;
        background-color: #fff;
        border-color: #333;
      }
      .page-link:hover{
          z-index: 3;
          color: #fff;
          background-color: #333;
          border-color: #333;
      }
      .disabled>.page-link, .page-link.disabled {
        border-color: #333;
      }
    </style>
    @yield('head')
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
          <a class="navbar-brand" href="/">OUTREACH</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/open-ai">Open AI</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/mailboxes">Mailboxes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/lists">Lists</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/leads">Leads</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="/campaigns">Campaigns</a>
              </li>
              {{-- <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/emails">Emails</a>
              </li> --}}
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/sent">Sent</a>
              </li>
              @php
                  $reliesNotSeen = App\Models\Reply::where('seen', '<', 1)->count();
              @endphp
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/inbox">Inbox {{ $reliesNotSeen? '('.$reliesNotSeen.')' : '' }}</a>
              </li>

            </ul>
          </div>
        </div>
      </nav>
      <div class="container pt-5">
        @yield('content')
      </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    @yield('footer')
  </body>
</html>