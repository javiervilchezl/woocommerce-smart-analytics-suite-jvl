document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('dateRangeForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            var startDate = document.getElementById('startDate').value;
            var endDate = document.getElementById('endDate').value;
            var compareYear = document.getElementById('compareYear').value;

            // Validar que las fechas estén completas
            if (!startDate || !endDate) {
                alert('Por favor, completa ambas fechas.');
                event.preventDefault(); // Cancelar el envío del formulario
                return false;
            }

            // Crear objetos de fecha a partir de los valores del formulario
            var start = new Date(startDate);
            var end = new Date(endDate);

            // Validar que la fecha de inicio sea menor o igual que la fecha de fin
            if (start > end) {
                alert('La fecha de inicio no puede ser posterior a la fecha de fin.');
                event.preventDefault(); // Cancelar el envío del formulario
                return false;
            }

            // Validar que el año de inicio sea igual al año de fin
            if (start.getFullYear() !== end.getFullYear()) {
                alert('La fecha de inicio y la fecha de fin deben estar dentro del mismo año.');
                event.preventDefault(); // Cancelar el envío del formulario
                return false;
            }

            // Validar que el año seleccionado para comparar sea distinto al año de las fechas de inicio y fin
            if (parseInt(compareYear) === start.getFullYear()) {
                alert('El año para comparar debe ser diferente al año de las fechas seleccionadas.');
                event.preventDefault(); // Cancelar el envío del formulario
                return false;
            }
        }); 
    } else {
        console.error('El formulario no se encontró. Asegúrate de que el ID esté correcto y el script se cargue correctamente.');
    }
});