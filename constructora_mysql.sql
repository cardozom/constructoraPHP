
-- Script convertido de SQL Server a MySQL

DROP DATABASE IF EXISTS constructora;
CREATE DATABASE constructora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE constructora;

CREATE TABLE Bancos (
    BancoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Banco VARCHAR(50) NOT NULL,
    Direccion VARCHAR(50) NOT NULL
);

CREATE TABLE BancosCuentas (
    CuentaId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    BancoId INT NOT NULL,
    Cuenta VARCHAR(50) NOT NULL,
    Saldo DECIMAL(18,2) NULL,
    FOREIGN KEY (BancoId) REFERENCES Bancos(BancoId)
);

CREATE TABLE BancosCuentasMovimientos (
    MovimientoId INT AUTO_INCREMENT PRIMARY KEY,
    CuentaId INT NOT NULL,
    Fecha DATETIME NOT NULL,
    Movimiento CHAR(3) NOT NULL,
    Importe DECIMAL(18,2) NOT NULL,
    Detalle VARCHAR(100) NULL,
    FOREIGN KEY (CuentaId) REFERENCES BancosCuentas(CuentaId)
);

CREATE TABLE ChequesLibrados (
    ChequeId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    CuentaId INT NOT NULL,
    ChequeNro VARCHAR(20) NULL,
    Debitado INT NULL,
    BancoId INT NOT NULL,
    Cuenta VARCHAR(50) NOT NULL,
    Monto DECIMAL(18,2) NOT NULL,
    FechaAcreditacion DATETIME NOT NULL,
    FechaEmision DATETIME NULL,
    ProveedorId INT NULL,
    MovimientoId INT NULL,
    Origen CHAR(10) NULL,
    PagoId INT NULL,
    FOREIGN KEY (CuentaId) REFERENCES BancosCuentas(CuentaId)
);

CREATE TABLE ChequesRecibidosTerceros (
    ChequeId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NULL,
    ChequeNro VARCHAR(20) NOT NULL,
    BancoId INT NOT NULL,
    Cuenta VARCHAR(50) NULL,
    Monto DECIMAL(18,2) NOT NULL,
    Emisor VARCHAR(50) NULL,
    Pagador VARCHAR(50) NULL,
    FechaRecepcion DATETIME NOT NULL,
    FechaEntrega DATETIME NULL,
    FechaAcreditacion DATETIME NOT NULL,
    Debitado INT NULL,
    ProveedorId INT NULL,
    MovimientoId INT NULL,
    MovimientoIdPago INT NULL,
    Origen CHAR(10) NULL,
    DepositoCuentaId INT NULL,
    Acreditado CHAR(2) NULL,
    PagoId INT NULL
);

CREATE TABLE Clientes (
    ClienteId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Cliente VARCHAR(50) NOT NULL,
    CondicionIvaId INT NOT NULL,
    CUIT CHAR(13) NOT NULL,
    Direccion VARCHAR(50) NOT NULL,
    Localidad VARCHAR(50) NOT NULL,
    Provincia VARCHAR(50) NOT NULL,
    Telefono1 VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    Habilitado CHAR(2) NOT NULL,
    Saldo DECIMAL(18,2) NOT NULL
);

CREATE TABLE CompraDetalle (
    CompraDetalleid INT AUTO_INCREMENT PRIMARY KEY,
    CompraId INT NOT NULL,
    EmpresaId INT NOT NULL,
    MaterialId INT NOT NULL,
    Cantidad INT NOT NULL,
    Importe DECIMAL(18,2) NOT NULL
);

CREATE TABLE Compras (
    CompraId INT AUTO_INCREMENT PRIMARY KEY,
    ProveedorId INT NOT NULL,
    EmpresaId INT NOT NULL,
    MaterialId INT NOT NULL,
    Fecha DATETIME NOT NULL,
    Letra CHAR(1) NULL,
    Factura VARCHAR(13) NULL,
    Remito VARCHAR(13) NOT NULL,
    Cantidad INT NOT NULL,
    Neto DECIMAL(18,2) NULL,
    IVA10 DECIMAL(18,2) NULL,
    IVA21 DECIMAL(18,2) NULL,
    RetencionIVA DECIMAL(18,2) NULL,
    Total DECIMAL(18,2) NOT NULL,
    Descripcion VARCHAR(50) NOT NULL,
    MontoUtilizado DECIMAL(18,2) NULL,
    EjercicioNro INT NULL
);

CREATE TABLE ConfiguracionesCertificados (
    ConfiguracionCertificadoId INT AUTO_INCREMENT PRIMARY KEY,
    ConfiguracionCertificado VARCHAR(50) NOT NULL,
    EmpresaId INT NULL
);

CREATE TABLE Contratistas (
    ContratistaId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Proveedor VARCHAR(50) NULL,
    CondicionIvaId INT NOT NULL,
    CUIT CHAR(13) NULL,
    CAE VARCHAR(20) NULL,
    Direccion VARCHAR(50) NOT NULL,
    Localidad VARCHAR(50) NOT NULL,
    Provincia VARCHAR(50) NOT NULL,
    Telefono1 VARCHAR(50) NULL,
    email VARCHAR(50) NULL,
    Habilitado CHAR(2) NULL,
    Saldo DECIMAL(18,2) NULL
);

CREATE TABLE CuentasBanco (
    CuentaId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Banco VARCHAR(50) NOT NULL,
    Cuenta VARCHAR(50) NOT NULL,
    Saldo DECIMAL(18,2) NULL
);

CREATE TABLE EgresosCertificados (
    CertificadosObraId INT AUTO_INCREMENT PRIMARY KEY,
    ConfiguracionCertificadoId INT NOT NULL,
    CertificadosObra VARCHAR(50) NOT NULL,
    Orden INT NOT NULL
);

CREATE TABLE EgresosRubros (
    EgresosRubrosId INT AUTO_INCREMENT PRIMARY KEY,
    CertificadosObraId INT NOT NULL,
    RubroId INT NOT NULL,
    Rubro VARCHAR(50) NOT NULL,
    Orden INT NOT NULL,
    UnidadId INT NOT NULL
);

CREATE TABLE EgresosSubRubros (
    RubroId INT NOT NULL,
    SubRubroId INT AUTO_INCREMENT PRIMARY KEY,
    SubRubro VARCHAR(50) NOT NULL,
    Orden INT NOT NULL,
    UnidadId INT NOT NULL
);

CREATE TABLE Empleados (
    EmpleadoId INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Empleado VARCHAR(50) NOT NULL,
    Categoria VARCHAR(50) NULL,
    ValorHora DECIMAL(18,2) NULL,
    Jornal DECIMAL(18,2) NOT NULL,
    Habilitado CHAR(2) NOT NULL
);

CREATE TABLE Empresa_Ejercicio (
    Ejercicio INT AUTO_INCREMENT PRIMARY KEY,
    EmpresaId INT NOT NULL,
    Fecha_Inicio DATETIME NOT NULL,
    Fecha_Final DATETIME NOT NULL,
    SaldoInicial DECIMAL(18,2) NULL,
    SaldoFinal DECIMAL(18,2) NULL,
    Resultado DECIMAL(18,2) NULL
);

CREATE TABLE EmpresaConstructora (
    EmpresaId INT AUTO_INCREMENT PRIMARY KEY,
    Empresa VARCHAR(50) NOT NULL,
    Cuit VARCHAR(13) NOT NULL,
    Direccion VARCHAR(50) NOT NULL,
    Localidad VARCHAR(50) NOT NULL,
    Saldo DECIMAL(18,2) NOT NULL,
    Ejercicio INT NOT NULL,
    FechaInicioEj DATETIME NOT NULL,
    FechaHabilitacion DATETIME NOT NULL
);
