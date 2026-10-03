@php
    $integration = $recaptchaIntegration ?? config('recaptcha.integration', 'classic_v3');
    $v3Integrations = ['enterprise_v3', 'classic_v3'];
@endphp

@if(in_array($integration, $v3Integrations, true))
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" value="">
    <p class="small text-muted text-center mb-0">
        Verificación reCAPTCHA al enviar el formulario (v3).
    </p>
@else
    <div class="d-flex justify-content-center">
        @if($integration === 'classic_v2')
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        @else
            <div id="recaptcha-enterprise-slot"></div>
        @endif
    </div>
@endif
@error('recaptcha')<div class="text-danger small text-center mt-2">{{ $message }}</div>@enderror

@if(!empty($recaptchaEnabled) && !empty($recaptchaSiteKey))
    @push('scripts')
        @if($integration === 'classic_v3')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
            <script>
                (function () {
                    const form = document.getElementById('login-form');
                    const field = document.getElementById('g-recaptcha-response');
                    const siteKey = @json($recaptchaSiteKey);
                    if (!form || !field || !siteKey) return;

                    form.addEventListener('submit', function (e) {
                        if (field.value) return;
                        e.preventDefault();
                        grecaptcha.ready(function () {
                            grecaptcha.execute(siteKey, { action: 'login' }).then(function (token) {
                                field.value = token;
                                form.submit();
                            }).catch(function () {
                                alert('No se pudo completar reCAPTCHA. Recargue la página e intente de nuevo.');
                            });
                        });
                    });
                })();
            </script>
        @elseif($integration === 'enterprise_v3')
            <script src="https://www.google.com/recaptcha/enterprise.js?render={{ $recaptchaSiteKey }}"></script>
            <script>
                (function () {
                    const form = document.getElementById('login-form');
                    const field = document.getElementById('g-recaptcha-response');
                    const siteKey = @json($recaptchaSiteKey);
                    if (!form || !field || !siteKey) return;

                    form.addEventListener('submit', function (e) {
                        if (field.value) return;
                        e.preventDefault();
                        grecaptcha.enterprise.ready(function () {
                            grecaptcha.enterprise.execute(siteKey, { action: 'login' }).then(function (token) {
                                field.value = token;
                                form.submit();
                            }).catch(function () {
                                alert('No se pudo completar reCAPTCHA. Recargue la página e intente de nuevo.');
                            });
                        });
                    });
                })();
            </script>
        @elseif($integration === 'classic_v2')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @else
            <script>
                function recaptchaEnterpriseOnload() {
                    grecaptcha.enterprise.render('recaptcha-enterprise-slot', {
                        sitekey: @json($recaptchaSiteKey),
                    });
                }
            </script>
            <script src="https://www.google.com/recaptcha/enterprise.js?render=explicit&onload=recaptchaEnterpriseOnload" async defer></script>
        @endif
    @endpush
@endif
