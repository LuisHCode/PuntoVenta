USE taller;

DELIMITER $$
--
-- Funciones
--
DROP PROCEDURE IF EXISTS buscarAdministrador$$
CREATE PROCEDURE buscarAdministrador (_id int, _idAdministrador varchar(15))
begin
    select * from administrador where id = _id or idAdministrador = _idAdministrador;
end$$

DROP PROCEDURE IF EXISTS filtrarAdministrador$$
CREATE PROCEDURE filtrarAdministrador (
    _parametros varchar(250), -- %idAdministrador%&%nombre%&%apellido1%&%apellido2%&
    _pagina SMALLINT UNSIGNED, 
    _cantRegs SMALLINT UNSIGNED)
begin
    SELECT cadenaFiltro(_parametros, 'idAdministrador&nombre&apellido1&apellido2&correo') INTO @filtro;
    SELECT concat("SELECT * from administrador where ", @filtro, " LIMIT ", 
        _pagina, ", ", _cantRegs) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
end$$

DROP PROCEDURE IF EXISTS numRegsAdministrador$$
CREATE PROCEDURE numRegsAdministrador (
    _parametros varchar(250))
begin
    SELECT cadenaFiltro(_parametros, 'idAdministrador&nombre&apellido1&apellido2&') INTO @filtro;
    SELECT concat("SELECT count(id) from administrador where ", @filtro) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
end$$

DROP FUNCTION IF EXISTS nuevoAdministrador$$
CREATE FUNCTION nuevoAdministrador (
    _idAdministrador Varchar(15),
    _nombre Varchar (30),
    _apellido1 Varchar (15),
    _apellido2 Varchar (15),
    _telefono Varchar (9),
    _celular Varchar (9),
    _direccion Varchar (255),
    _correo Varchar (100))
    RETURNS INT(1) 
begin
    declare _cant int;
    select count(id) into _cant from administrador where idAdministrador = _idAdministrador OR correo = _correo;
    if _cant < 1 then
        insert into administrador(idAdministrador, nombre, apellido1, apellido2, telefono, 
            celular, direccion, correo) 
            values (_idAdministrador, _nombre, _apellido1, _apellido2, _telefono, 
            _celular, _direccion, _correo);
    end if;
    return _cant;
end$$


DROP FUNCTION IF EXISTS editarAdministrador$$
CREATE FUNCTION editarAdministrador (
    _id int, 
    _idAdministrador Varchar(15),
    _nombre Varchar (30),
    _apellido1 Varchar (15),
    _apellido2 Varchar (15),
    _telefono Varchar (9),
    _celular Varchar (9),
    _direccion Varchar (255),
    _correo  Varchar (100)
    ) RETURNS INT(1) 
begin
    declare _cant int;
    select count(id) into _cant from administrador where id = _id;
    if _cant > 0 then
        update administrador set
            idAdministrador = _idAdministrador,
            nombre = _nombre,
            apellido1 = _apellido1,
            apellido2 = _apellido2,
            telefono = _telefono,
            celular = _celular,
            direccion = _direccion,
            correo = _correo
        where id = _id;
    end if;
    return _cant;
end$$

DROP FUNCTION IF EXISTS eliminarAdministrador$$
CREATE FUNCTION eliminarAdministrador (_id INT(1)) RETURNS INT(1)
begin
    declare _cant int;
    declare _resp int;
    SELECT 0 into _resp;
    select count(id) into _cant from administrador where id = _id;
    if _cant > 0 then
        delete from administrador where id = _id;
        select 1 into _resp;
    end if;
    return _resp;
end$$

DELIMITER ;
