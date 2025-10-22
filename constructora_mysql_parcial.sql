
-- Conversión parcial de SQL Server a MySQL

CREATE DATABASE IF NOT EXISTS constructora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE constructora;

CREATE TABLE EmpresaMovimientos (
    MovimientoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    ObraId INT NOT NULL,
    ObraMovimientoId INT NULL,
    Movimiento VARCHAR(200) NOT NULL,
    FechaMovimiento DATETIME NOT NULL,
    Valor DECIMAL(18,2) NOT NULL,
    TipoMovimiento VARCHAR(8) NULL,
    TiposCostoId INT NULL,
    Ejercicio INT NULL,
    proveedorId INT NULL,
    TipoMovimientoiD INT NULL
);

CREATE TABLE entCert (
    ObraEgresoPresupuestoId INT NOT NULL,
    CertificadosObraId INT NOT NULL,
    RubroId INT NOT NULL,
    SubRubroId INT NOT NULL,
    CertificadosObra VARCHAR(50) NULL,
    Rubro VARCHAR(50) NULL,
    SubRubro VARCHAR(50) NULL,
    Proyectado DECIMAL(18,2) NULL,
    Presupuestado DECIMAL(18,2) NULL,
    FechaInicio DATETIME NULL,
    FechaFin DATETIME NULL,
    FechaInicio_Presup DATETIME NULL,
    FechaFin_Presup DATETIME NULL,
    UnidadId INT NULL,
    Unidad VARCHAR(30) NULL,
    Cantidad INT NULL
);

CREATE TABLE EstadoObras (
    EstadoObraId INT AUTO_INCREMENT PRIMARY KEY,
    EstadoObra VARCHAR(50) NOT NULL
);

CREATE TABLE EtapasObras (
    EtapaId INT AUTO_INCREMENT PRIMARY KEY,
    ObraId INT NULL,
    Etapa VARCHAR(50) NOT NULL,
    FechaInicial DATETIME NULL,
    FechaFinal DATETIME NULL,
    Presupuestado DECIMAL(18,2) NULL,
    UnidadId INT NULL,
    Cantidad INT NULL
);

CREATE TABLE ObraEgresoPresupuesto (
    ObraEgresoPresupuestoId INT AUTO_INCREMENT PRIMARY KEY,
    ObraId INT NOT NULL,
    CertificadosObraId INT NOT NULL,
    RubroId INT NOT NULL,
    SubRubroId INT NOT NULL,
    Proyectado DECIMAL(18,2) NULL,
    Presupuestado DECIMAL(18,2) NULL,
    FechaInicio DATETIME NULL,
    FechaFin DATETIME NULL,
    FechaInicio_Presup DATETIME NULL,
    FechaFin_Presup DATETIME NULL,
    UnidadId INT NULL,
    Cantidad INT NULL
);

CREATE TABLE Obras (
    ObraId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Obra VARCHAR(200) NOT NULL,
    Fecha_inicio DATETIME NOT NULL,
    Contacto VARCHAR(50) NULL,
    Telefono VARCHAR(50) NULL,
    Mail VARCHAR(50) NULL,
    EstadoObraId INT NOT NULL,
    Saldo DECIMAL(18,2) NOT NULL,
    Fecha_Cierre DATETIME NULL,
    Etapas INT NULL,
    ConfiguracionCertificadoId INT NULL,
    Precio DECIMAL(18,2) NULL
);

CREATE TABLE Obras_Empleados (
    EmpleadoId INT NOT NULL,
    ObraId INT NOT NULL
);

CREATE TABLE Obras_Empleados_Adelantos (
    AdelantoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpleadoId INT NOT NULL,
    FechaAdelantos DATETIME NOT NULL,
    Adelanto DECIMAL(18,2) NOT NULL,
    Contabilizado INT NOT NULL
);

CREATE TABLE Obras_Empleados_Gastos (
    MovimientoId INT NOT NULL,
    ObraId INT NOT NULL,
    EmpleadoId INT NOT NULL,
    FechaMovimiento DATETIME NOT NULL,
    Jornal DECIMAL(18,2) NOT NULL,
    Dias DECIMAL(4,1) NOT NULL,
    TotalEmpleado DECIMAL(18,2) NOT NULL,
    Pagado INT NOT NULL
);

CREATE TABLE ObrasMovimientos (
    MovimientoId INT AUTO_INCREMENT PRIMARY KEY,
    ObraId INT NOT NULL,
    Movimiento VARCHAR(200) NOT NULL,
    FechaMovimiento DATETIME NOT NULL,
    Valor DECIMAL(18,2) NOT NULL,
    Cantidad INT NULL,
    TipoMovimiento VARCHAR(8) NULL,
    Origen CHAR(2) NULL,
    TipoCosto CHAR(2) NOT NULL,
    TipoIngreso INT NULL,
    CostoId INT NOT NULL,
    EtapaId INT NULL,
    ObraEgresoPresupuestoId INT NULL,
    ProveedorId INT NULL,
    Remito VARCHAR(13) NULL,
    EmpleadoId INT NULL,
    Pagado INT NULL
);

CREATE TABLE Pagos (
    PagoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    ProveedorId INT NOT NULL,
    Fecha DATETIME NOT NULL,
    TipoPago INT NULL,
    Monto DECIMAL(18,2) NOT NULL,
    Descripcion VARCHAR(50) NULL
);

CREATE TABLE Proveedores (
    proveedorId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Proveedor VARCHAR(50) NULL,
    CondicionIvaId INT NOT NULL,
    CUIT CHAR(13) NULL,
    Direccion VARCHAR(50) NOT NULL,
    Localidad VARCHAR(50) NOT NULL,
    Provincia VARCHAR(50) NOT NULL,
    Telefono1 VARCHAR(50) NULL,
    email VARCHAR(50) NULL,
    Habilitado CHAR(2) NULL,
    Saldo DECIMAL(18,2) NULL
);

CREATE TABLE tblBancos (
    BancoId INT AUTO_INCREMENT PRIMARY KEY,
    Banco VARCHAR(50) NULL
);

CREATE TABLE tblCodigoCosto (
    TipoCosto CHAR(2) NULL,
    TipoCostoDescripcion VARCHAR(50) NULL
);

CREATE TABLE tblCondicionIva (
    CondicionIvaId INT AUTO_INCREMENT PRIMARY KEY,
    CondicionIva VARCHAR(25) NOT NULL
);

CREATE TABLE tblEjercicio (
    EjercicioNro INT NOT NULL,
    EmpresaId INT NULL,
    FechaInicio DATETIME NOT NULL,
    FechaCierre DATETIME NOT NULL
);

CREATE TABLE tblTipoCosto (
    TipoCosto CHAR(2) NOT NULL PRIMARY KEY,
    TipoCostoDescripcion VARCHAR(50) NOT NULL
);

CREATE TABLE tblUnidades (
    UnidadId INT AUTO_INCREMENT PRIMARY KEY,
    Unidad VARCHAR(30) NOT NULL
);

CREATE TABLE TiposCostoEmpresa (
    CostoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Costo VARCHAR(50) NULL
);

CREATE TABLE TiposCostoObra (
    CostoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Costo VARCHAR(50) NOT NULL,
    TipoCosto CHAR(2) NOT NULL
);

CREATE TABLE tmpEmpresaMovimientos (
    MovimientoId INT NOT NULL,
    Obra VARCHAR(200) NULL,
    Movimiento VARCHAR(200) NOT NULL,
    FMovimiento DATETIME NOT NULL,
    Valor VARCHAR(30) NULL,
    TipoMovimiento VARCHAR(8) NULL
);

CREATE TABLE Usuarios (
    UsuarioId INT AUTO_INCREMENT PRIMARY KEY,
    Usuario VARCHAR(50) NOT NULL,
    Clave VARCHAR(50) NOT NULL,
    EmpresaId INT NOT NULL,
    Sistema CHAR(10) NOT NULL,
    FechaHabilitacion DATETIME NOT NULL,
    Perfil CHAR(10) NOT NULL
);
