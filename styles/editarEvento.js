document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');

  fetch(`../admin/obtenerEventoPorId.php?id=${id}`)
    .then(res => res.json())
    .then(evento => {
      document.getElementById('id_eve_cur').value = evento.id_eve_cur;
      document.getElementById('tit_eve_cur').value = evento.tit_eve_cur;
      document.getElementById('des_eve_cur').value = evento.des_eve_cur;
      document.getElementById('fec_ini_eve_cur').value = evento.fec_ini_eve_cur;
      document.getElementById('fec_fin_eve_cur').value = evento.fec_fin_eve_cur;
      document.getElementById('cos_eve_cur').value = evento.cos_eve_cur;
      document.getElementById('tip_eve').value = evento.tip_eve;
      document.getElementById('mod_eve_cur').value = evento.mod_eve_cur;
    });

  document.getElementById('form-editar').addEventListener('submit', function(e) {
    e.preventDefault();

    const datos = {
      id: document.getElementById('id_eve_cur').value,
      titulo: document.getElementById('tit_eve_cur').value,
      descripcion: document.getElementById('des_eve_cur').value,
      fecha_inicio: document.getElementById('fec_ini_eve_cur').value,
      fecha_fin: document.getElementById('fec_fin_eve_cur').value,
      costo: document.getElementById('cos_eve_cur').value,
      tipo: document.getElementById('tip_eve').value,
      modalidad: document.getElementById('mod_eve_cur').value
    };

    fetch('../admin/actualizarEvento.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(data => {
      alert(data.mensaje);
      window.location.href = '../admin/verEventos.html';
    })
    .catch(err => {
      console.error(err);
      alert('Error al actualizar el evento');
    });
  });
});
