<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Reestablecer Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f3f6fb;
            --card:#ffffff;
            --accent:#4f46e5;
            --accent-2:#06b6d4;
            --muted:#7b8794;
            --danger:#ef4444;
            --success:#10b981;
            --radius:12px;
            --shadow: 0 8px 30px rgba(16,24,40,0.08);
        }
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial;
            background:linear-gradient(180deg,#eef2ff 0%, var(--bg) 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:28px;
            color:#0f172a;
        }
        .wrap{
            width:100%;
            max-width:760px;
            display:grid;
            grid-template-columns: 1fr 420px;
            gap:28px;
            align-items:center;
        }

        .hero{
            padding:32px;
            border-radius:var(--radius);
            background:linear-gradient(180deg, rgba(79,70,229,0.06), rgba(6,182,212,0.03));
            box-shadow:var(--shadow);
        }
        .hero h1{margin:0 0 8px;font-size:22px;letter-spacing:-0.2px}
        .hero p{margin:0;color:var(--muted);line-height:1.5}

        .card{
            background:var(--card);
            border-radius:var(--radius);
            padding:24px;
            box-shadow:var(--shadow);
            transition:transform .22s ease, box-shadow .22s ease;
            border: 2px solid black;
        }
        .card:hover{transform:translateY(-4px)}

        label{display:block;margin:12px 0 8px;font-weight:600;font-size:13px;color:#0b1220}
        input[type="text"], input[type="password"]{
            width:100%;
            padding:12px 14px;
            border:1px solid #e6e9ee;
            border-radius:10px;
            font-size:14px;
            color:#071233;
            background:linear-gradient(180deg,#fff,#fbfdff);
            transition:border-color .12s ease, box-shadow .12s ease;
        }
        input:focus{outline:none;border-color:rgba(79,70,229,0.7);box-shadow:0 4px 18px rgba(79,70,229,0.06)}
        .row{display:flex;gap:12px}
        .hidden{display:none!important}
        .msg{
            margin-top:12px;
            padding:10px 12px;
            border-radius:10px;
            font-size:13px;
            display:none;
        }
        .msg.show{display:block}
        .msg.error{background:#fff7f7;color:var(--danger);border:1px solid rgba(239,68,68,0.08)}
        .msg.success{background:#f4fffb;color:var(--success);border:1px solid rgba(16,185,129,0.08)}

        .strength{
            height:10px;
            background:#f1f5f9;
            border-radius:8px;
            overflow:hidden;
            margin-top:8px;
        }
        .strength > i{
            display:block;height:100%;
            transition:width .18s ease, background .18s ease;
            width:0%;
            background:linear-gradient(90deg,var(--danger),#ff8a80);
        }
        .strength.medium > i{width:66%; background:linear-gradient(90deg,#f59e0b,#f97316)}
        .strength.strong > i{width:100%; background:linear-gradient(90deg,#06b6d4,#4f46e5)}

        .flex{display:flex;align-items:center;gap:10px}
        .toggle{cursor:pointer;font-size:18px;user-select:none}
        .btn{
            margin-top:16px;
            padding:12px 16px;
            border-radius:10px;
            border:none;
            cursor:pointer;
            font-weight:600;
            background:linear-gradient(90deg,var(--accent),var(--accent-2));
            color:#fff;
            box-shadow:0 8px 20px rgba(79,70,229,0.12);
            transition:transform .12s ease,opacity .12s ease;
        }
        .btn:active{transform:translateY(1px)}
        .btn[disabled]{opacity:.6;cursor:default;box-shadow:none}

        .small{font-size:13px;color:var(--muted);margin-top:8px}

        /* responsive */
        @media (max-width:900px){
            .wrap{grid-template-columns:1fr; padding:0 10px}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="hero">
            <h1>Reestablecer tu contraseña</h1>
            <p>Introduce el código que recibiste por correo. Luego crea una nueva contraseña segura para acceder a tu cuenta.</p>
            <p class="small">Consejo: usa al menos 8 caracteres, mezcla mayúsculas, números y símbolos.</p>
        </div>

        <div class="card" aria-live="polite">
            <!-- Paso 1: ingresar código -->
            <form id="codigo-form">
                <label for="codigo">Código recibido</label>
                <input id="codigo" name="codigo" type="text" autocomplete="off" required placeholder="Ej. 6G7H2K" />
                <div class="row" style="margin-top:10px;align-items:center">
                    <button class="btn" id="btn-verificar" type="submit">Verificar código</button>
                    <div style="flex:1"></div>
                </div>
                <div id="codigo-msg" class="msg error" role="alert" aria-hidden="true"></div>
            </form>

            <!-- Paso 2: formulario para nueva contraseña (oculto inicialmente) -->
            <form id="pass-form" class="hidden" novalidate>
                <input type="hidden" id="codigo-hidden" name="codigo" />
                <label for="contrasena">Nueva contraseña</label>
                <div class="flex" style="align-items:center">
                    <input id="contrasena" name="contrasena" type="password" required placeholder="Nueva contraseña" />
                    <span id="togglePassword" class="toggle" title="Mostrar / ocultar">👁️</span>
                </div>
                <div id="strength" class="strength" aria-hidden="true"><i></i></div>

                <label for="contrasena2">Confirmar contraseña</label>
                <input id="contrasena2" name="contrasena2" type="password" required placeholder="Repite la contraseña" />

                <div class="row" style="margin-top:8px;align-items:center">
                    <button class="btn" id="btn-reset" type="submit">Guardar nueva contraseña</button>
                    <div style="flex:1"></div>
                </div>

                <div id="pass-msg" class="msg error" role="alert" aria-hidden="true"></div>
                <div id="pass-success" class="msg success" role="status" aria-hidden="true"></div>
            </form>
        </div>
    </div>

    <script>
    (function(){
        const codigoForm = document.getElementById('codigo-form');
        const passForm = document.getElementById('pass-form');
        const codigoInput = document.getElementById('codigo');
        const codigoHidden = document.getElementById('codigo-hidden');
        const codigoMsg = document.getElementById('codigo-msg');

        const contrasena = document.getElementById('contrasena');
        const contrasena2 = document.getElementById('contrasena2');
        const togglePassword = document.getElementById('togglePassword');
        const strengthBar = document.getElementById('strength');
        const passMsg = document.getElementById('pass-msg');
        const passSuccess = document.getElementById('pass-success');
        const btnVerificar = document.getElementById('btn-verificar');
        const btnReset = document.getElementById('btn-reset');

        // toggle visibility
        togglePassword.addEventListener('click', () => {
            const t = contrasena.type === 'password' ? 'text' : 'password';
            contrasena.type = t;
            togglePassword.textContent = t === 'password' ? '👁️' : '🙈';
        });

        function updateStrength(pw){
            const el = strengthBar;
            el.className = 'strength';
            const i = el.querySelector('i');
            i.style.width = '0%';
            if (!pw) return;
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[\W_]/.test(pw)) score++;
            if (score <= 1) { el.classList.add('weak'); i.style.width = '33%'; i.style.background = 'linear-gradient(90deg,var(--danger),#ff8a80)'; }
            else if (score <= 3) { el.classList.add('medium'); i.style.width = '66%'; i.style.background = 'linear-gradient(90deg,#f59e0b,#f97316)'; el.classList.add('medium'); }
            else { el.classList.add('strong'); i.style.width = '100%'; i.style.background = 'linear-gradient(90deg,#06b6d4,#4f46e5)'; el.classList.add('strong'); }
        }

        contrasena.addEventListener('input', () => updateStrength(contrasena.value));

        // verify code (POST as application/x-www-form-urlencoded)
        codigoForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            codigoMsg.classList.remove('show');
            codigoMsg.setAttribute('aria-hidden','true');
            const code = codigoInput.value.trim();
            if (!code) {
                codigoMsg.textContent = 'Ingresa el código.';
                codigoMsg.classList.add('show');
                codigoMsg.setAttribute('aria-hidden','false');
                return;
            }

            btnVerificar.disabled = true;
            try {
                const body = new URLSearchParams({ codigo: code }).toString();
                const res = await fetch('../backend/verify_code.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();
                if (json.success) {
                    codigoHidden.value = code;
                    codigoForm.classList.add('hidden');
                    passForm.classList.remove('hidden');
                    contrasena.focus();
                } else {
                    codigoMsg.textContent = json.message || 'Código inválido.';
                    codigoMsg.classList.add('show');
                    codigoMsg.setAttribute('aria-hidden','false');
                }
            } catch (err) {
                codigoMsg.textContent = 'Error de conexión. Intente nuevamente.';
                codigoMsg.classList.add('show');
                codigoMsg.setAttribute('aria-hidden','false');
            } finally {
                btnVerificar.disabled = false;
            }
        });

        // submit new password
        passForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            passMsg.classList.remove('show');
            passMsg.setAttribute('aria-hidden','true');
            passSuccess.classList.remove('show');
            passSuccess.setAttribute('aria-hidden','true');

            const pw = contrasena.value;
            const pw2 = contrasena2.value;
            const code = codigoHidden.value;

            if (pw.length < 8) {
                passMsg.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                passMsg.classList.add('show');
                passMsg.setAttribute('aria-hidden','false');
                return;
            }
            if (pw !== pw2) {
                passMsg.textContent = 'Las contraseñas no coinciden.';
                passMsg.classList.add('show');
                passMsg.setAttribute('aria-hidden','false');
                return;
            }

            btnReset.disabled = true;
            try {
                const body = new URLSearchParams({ codigo: code, contrasena: pw }).toString();
                const res = await fetch('../backend/reset_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();
                if (json.success) {
                    passSuccess.textContent = json.message || 'Contraseña reestablecida correctamente.';
                    passSuccess.classList.add('show');
                    passSuccess.setAttribute('aria-hidden','false');
                    passForm.reset();
                    updateStrength('');
                    setTimeout(()=> window.location.href = 'login.php', 1500);
                } else {
                    passMsg.textContent = json.message || 'No se pudo reestablecer la contraseña.';
                    passMsg.classList.add('show');
                    passMsg.setAttribute('aria-hidden','false');
                }
            } catch (err) {
                passMsg.textContent = 'Error de conexión. Intente nuevamente.';
                passMsg.classList.add('show');
                passMsg.setAttribute('aria-hidden','false');
            } finally {
                btnReset.disabled = false;
            }
        });

    })();
    </script>
</body>
</html>