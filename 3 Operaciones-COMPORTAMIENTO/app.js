// RECEPTOR: Calculadora
class Calculadora {
    constructor() {
      this.resultado = 0;
    }
  
    sumar(a, b) {
      this.resultado = a + b;
      return this.resultado;
    }
  
    restar(a, b) {
      this.resultado = a - b;
      return this.resultado;
    }
  
    getResultado() {
      return this.resultado;
    }
  
    setResultado(valor) {
      this.resultado = valor;
    }
  }
  
  // PATRÓN COMMAND: Comandos concretos
  class ComandoSumar {
    constructor(calculadora, a, b) {
      this.calculadora = calculadora;
      this.a = a;
      this.b = b;
    }
  
    ejecutar() {
      return this.calculadora.sumar(this.a, this.b);
    }
  }
  
  class ComandoRestar {
    constructor(calculadora, a, b) {
      this.calculadora = calculadora;
      this.a = a;
      this.b = b;
    }
  
    ejecutar() {
      return this.calculadora.restar(this.a, this.b);
    }
  }
  
  // PATRÓN MEMENTO: Guarda el estado anterior
  class Memento {
    constructor(estado) {
      this.estado = estado;
    }
  
    getEstado() {
      return this.estado;
    }
  }
  
  // CUIDADOR del Memento
  class Historial {
    constructor() {
      this.pila = [];
    }
  
    guardar(memento) {
      this.pila.push(memento);
    }
  
    deshacer() {
      return this.pila.pop();
    }
  }
  
  // CONTROLADOR PRINCIPAL
  const calculadora = new Calculadora();
  const historial = new Historial();
  
  function ejecutarComando(operacion) {
    const a = parseFloat(document.getElementById("numero1").value);
    const b = parseFloat(document.getElementById("numero2").value);
  
    if (isNaN(a) || isNaN(b)) {
      document.getElementById("resultado").innerText = "Por favor, ingresa números válidos.";
      return;
    }
  
    let comando;
  
    // Guardamos el estado anterior antes de ejecutar el nuevo comando
    historial.guardar(new Memento(calculadora.getResultado()));
  
    if (operacion === "sumar") {
      comando = new ComandoSumar(calculadora, a, b);
    } else if (operacion === "restar") {
      comando = new ComandoRestar(calculadora, a, b);
    }
  
    const resultado = comando.ejecutar();
    document.getElementById("resultado").innerText = `Resultado: ${resultado}`;
  }
  
  function deshacer() {
    const memento = historial.deshacer();
    if (memento) {
      calculadora.setResultado(memento.getEstado());
      document.getElementById("resultado").innerText = `Resultado: ${calculadora.getResultado()}`;
    } else {
      document.getElementById("resultado").innerText = "Nada que deshacer.";
    }
  }
  