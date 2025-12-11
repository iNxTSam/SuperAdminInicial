function limpiarFormulario() {
    let msg = document.getElementById('textMsg');
    let inf = document.getElementById('textInf');
    let pass = document.getElementById('textPass');
    document.getElementById('form').reset();
    if (msg || inf || pass) {
        msg.textContent = '';
        inf.textContent = '';
        pass.textContent = '';
    }
}