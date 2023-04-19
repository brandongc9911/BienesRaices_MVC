<fieldset>
    <legend>Información general</legend>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="vendedor[nombre]" placeholder="Nombre" value="<?php echo s($vendedor->nombre); ?>">

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="vendedor[apellido]" placeholder="apellido" value="<?php echo s($vendedor->apellido); ?>">
</fieldset>


<fieldset>
    <legend>Información Extra</legend>
    <label for="teléfono">Teléfono:</label>
    <input type="text" id="teléfono" name="vendedor[telefono]" placeholder="teléfono" value="<?php echo s($vendedor->telefono); ?>">


</fieldset>