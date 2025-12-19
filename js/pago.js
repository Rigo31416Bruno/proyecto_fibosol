document.addEventListener("DOMContentLoaded", () => {
  const paymentForm = document.getElementById("paymentForm")
  const cardNumberInput = document.getElementById("cardNumber")
  const expiryInput = document.getElementById("expiry")
  const cvvInput = document.getElementById("cvv")
  const cardHolderInput = document.getElementById("cardHolder")

  let lastPedidoId = null

  function showToast(title, message, type = "info") {
    const container = document.getElementById("toastContainer")
    const toast = document.createElement("div")
    toast.className = `toast toast-${type}`

    const icons = {
      error: "✕",
      warning: "⚠",
      success: "✓",
      info: "ℹ",
    }

    toast.innerHTML = `
      <div class="toast-icon">${icons[type] || icons.info}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close" aria-label="Cerrar">×</button>
      <div class="toast-progress"></div>
    `

    container.appendChild(toast)

    const closeBtn = toast.querySelector(".toast-close")
    closeBtn.addEventListener("click", () => removeToast(toast))

    setTimeout(() => removeToast(toast), 5000)
  }

  function removeToast(toast) {
    toast.classList.add("toast-hiding")
    setTimeout(() => {
      if (toast.parentElement) {
        toast.parentElement.removeChild(toast)
      }
    }, 300)
  }

  if (cardNumberInput) {
    cardNumberInput.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\s/g, "")
      value = value.replace(/\D/g, "")
      const formattedValue = value.match(/.{1,4}/g)
      e.target.value = formattedValue ? formattedValue.join(" ") : value

      clearFieldError("cardNumber")
    })
  }

  if (expiryInput) {
    expiryInput.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\D/g, "")
      if (value.length >= 2) {
        value = value.substring(0, 2) + "/" + value.substring(2, 4)
      }
      e.target.value = value

      clearFieldError("expiry")
    })
  }

  if (cvvInput) {
    cvvInput.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/\D/g, "")
      clearFieldError("cvv")
    })
  }

  if (cardHolderInput) {
    cardHolderInput.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, "")
      clearFieldError("cardHolder")
    })
  }

  function clearFieldError(fieldName) {
    const errorElement = document.getElementById(`${fieldName}Error`)
    if (errorElement) {
      errorElement.textContent = ""
    }
    const input = document.getElementById(fieldName)
    if (input) {
      input.parentElement.classList.remove("error")
    }
  }

  function showFieldError(fieldName, message) {
    const errorElement = document.getElementById(`${fieldName}Error`)
    if (errorElement) {
      errorElement.textContent = message
    }
    const input = document.getElementById(fieldName)
    if (input) {
      const formGroup = input.closest(".form-group")
      if (formGroup) {
        formGroup.classList.add("error")
      }
    }
  }

  function luhnCheck(cardNumber) {
    // función obsoleta: validación por Luhn removida en favor de regla de 16 dígitos
    return false
  }

  function validateForm() {
    let isValid = true
    const errors = []

    // Validar número de tarjeta (debe tener exactamente 16 dígitos, sólo números)
    const cardNumber = cardNumberInput.value.replace(/\s/g, "")
    if (!cardNumber) {
      showFieldError("cardNumber", "El número de tarjeta es requerido")
      errors.push("Número de tarjeta requerido")
      isValid = false
    } else if (!/^\d{16}$/.test(cardNumber)) {
      showFieldError("cardNumber", "El número debe tener 16 dígitos")
      errors.push("Número de tarjeta con longitud incorrecta")
      isValid = false
    } else {
      clearFieldError("cardNumber")
    }

    // Validar titular
    const cardHolder = cardHolderInput.value.trim()
    if (!cardHolder) {
      showFieldError("cardHolder", "El nombre del titular es requerido")
      errors.push("Nombre del titular requerido")
      isValid = false
    } else if (cardHolder.length < 3) {
      showFieldError("cardHolder", "El nombre debe tener al menos 3 caracteres")
      errors.push("Nombre del titular muy corto")
      isValid = false
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(cardHolder)) {
      showFieldError("cardHolder", "Solo se permiten letras")
      errors.push("Nombre del titular con caracteres inválidos")
      isValid = false
    } else {
      clearFieldError("cardHolder")
    }

    // Validar fecha de vencimiento
    const expiry = expiryInput.value
    if (!expiry) {
      showFieldError("expiry", "La fecha de vencimiento es requerida")
      errors.push("Fecha de vencimiento requerida")
      isValid = false
    } else if (!expiry.match(/^\d{2}\/\d{2}$/)) {
      showFieldError("expiry", "Formato inválido (MM/AA)")
      errors.push("Formato de fecha inválido")
      isValid = false
    } else {
      const [month, year] = expiry.split("/")
      const currentYear = new Date().getFullYear() % 100
      const currentMonth = new Date().getMonth() + 1
      const expMonth = Number.parseInt(month)
      const expYear = Number.parseInt(year)

      if (expMonth < 1 || expMonth > 12) {
        showFieldError("expiry", "Mes inválido (01-12)")
        errors.push("Mes inválido")
        isValid = false
      } else if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
        showFieldError("expiry", "La tarjeta está vencida")
        errors.push("Tarjeta vencida")
        isValid = false
      } else {
        clearFieldError("expiry")
      }
    }

    // Validar CVV
    const cvv = cvvInput.value
    if (!cvv) {
      showFieldError("cvv", "El CVV es requerido")
      errors.push("CVV requerido")
      isValid = false
    } else if (cvv.length !== 3 && cvv.length !== 4) {
      showFieldError("cvv", "El CVV debe tener 3 o 4 dígitos")
      errors.push("CVV con longitud incorrecta")
      isValid = false
    } else if (!/^\d+$/.test(cvv)) {
      showFieldError("cvv", "Solo se permiten números")
      errors.push("CVV con caracteres inválidos")
      isValid = false
    } else {
      clearFieldError("cvv")
    }

    if (!isValid) {
      showToast("Error en la validación", "Por favor corrige los errores en el formulario", "error")
    }

    return isValid
  }

  if (paymentForm) {
    paymentForm.addEventListener("submit", (e) => {
      e.preventDefault()

      console.log("[v0] Validando formulario de pago")

      if (!validateForm()) {
        console.log("[v0] Validación fallida")
        return
      }

      console.log("[v0] Validación exitosa, mostrando modal de confirmación")
      showConfirmModal()
    })
  }

  function showConfirmModal() {
    const modal = document.getElementById("confirmPaymentModal")
    if (modal) {
      modal.classList.add("show")
    }
  }

  const confirmBtn = document.getElementById("confirmPaymentBtn")
  if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
      console.log("[v0] Usuario confirmó el pago")
      const modal = document.getElementById("confirmPaymentModal")
      if (modal) {
        modal.classList.remove("show")
      }
      processPayment()
    })
  }

  const cancelBtn = document.getElementById("cancelPaymentBtn")
  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      console.log("[v0] Usuario canceló el pago")
      const modal = document.getElementById("confirmPaymentModal")
      if (modal) {
        modal.classList.remove("show")
      }
    })
  }

  window.addEventListener("click", (e) => {
    const confirmModal = document.getElementById("confirmPaymentModal")
    const successModal = document.getElementById("paymentSuccessModal")
    const errorModal = document.getElementById("errorModal")

    if (e.target === confirmModal) {
      confirmModal.classList.remove("show")
    }
    if (e.target === successModal) {
      successModal.classList.remove("show")
    }
    if (e.target === errorModal) {
      errorModal.classList.remove("show")
    }
  })

  function processPayment() {
    const btnPay = document.querySelector(".btn-pay")
    const btnText = btnPay.querySelector(".btn-text")
    const btnLoading = btnPay.querySelector(".btn-loading")

    console.log("[v0] Procesando pago...")

    btnText.style.display = "none"
    btnLoading.style.display = "flex"
    btnPay.disabled = true

    const formData = new FormData(paymentForm)

    fetch("../backend/procesarPago.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("[v0] Respuesta del servidor:", data)

        if (data.success) {
          lastPedidoId = data.pedido_id
          showSuccessModal(data.pedido_id)
          showToast("Pago exitoso", "Tu compra ha sido procesada correctamente", "success")
        } else {
          showErrorModal(data.message || "Error al procesar el pago")
          showToast("Error en el pago", data.message || "No se pudo procesar el pago", "error")
        }
      })
      .catch((error) => {
        console.error("[v0] Error de red:", error)
        showErrorModal("Error de conexión al procesar el pago")
        showToast("Error de conexión", "No se pudo conectar con el servidor", "error")
      })
      .finally(() => {
        btnText.style.display = "inline"
        btnLoading.style.display = "none"
        btnPay.disabled = false
      })
  }

  function showSuccessModal(pedidoId) {
    const modal = document.getElementById("paymentSuccessModal")
    const message = document.getElementById("successMessage")

    if (message) {
      message.innerHTML = `Tu pago ha sido procesado correctamente.<br><strong>Pedido #${pedidoId}</strong>`
    }

    if (modal) {
      modal.classList.add("show")
    }
  }

  function showErrorModal(message) {
    const modal = document.getElementById("errorModal")
    const messageElement = document.getElementById("errorMessage")

    if (messageElement) {
      messageElement.textContent = message
    }

    if (modal) {
      modal.classList.add("show")
    }
  }

  const closeErrorBtn = document.getElementById("closeErrorBtn")
  if (closeErrorBtn) {
    closeErrorBtn.addEventListener("click", () => {
      const modal = document.getElementById("errorModal")
      if (modal) {
        modal.classList.remove("show")
      }
    })
  }

  const generateInvoiceBtn = document.getElementById("generateInvoiceBtn")
  if (generateInvoiceBtn) {
    generateInvoiceBtn.addEventListener("click", () => {
      if (!lastPedidoId) {
        showToast("Error", "No hay pedido para generar factura", "error")
        return
      }

      console.log("[v0] Generando factura para pedido:", lastPedidoId)

      generateInvoiceBtn.disabled = true
      const originalText = generateInvoiceBtn.innerHTML
      generateInvoiceBtn.innerHTML = '<span class="spinner"></span> Generando...'

      const formData = new FormData()
      formData.append("pedido_id", lastPedidoId)

      fetch("../backend/generarFactura.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((text) => {
          // Algunos errores de PHP pueden devolver HTML (empiezan con '<') y romper JSON.parse.
          try {
            const data = JSON.parse(text)
            console.log("[v0] Respuesta generación factura:", data)
            if (data.success && data.url) {
              window.open(data.url, "_blank")
              showToast("Factura generada", "La factura se ha generado correctamente", "success")
            } else {
              showToast("Error", data.message || "No se pudo generar la factura", "error")
            }
          } catch (err) {
            console.error('[v0] Respuesta no JSON al generar factura:', text)
            showToast('Error', 'Respuesta inválida del servidor al generar la factura. Revisa los logs.', 'error')
          }
        })
        .catch((error) => {
          console.error("[v0] Error generando factura:", error)
          showToast("Error de conexión", "No se pudo generar la factura", "error")
        })
        .finally(() => {
          generateInvoiceBtn.disabled = false
          generateInvoiceBtn.innerHTML = originalText
        })
    })
  }
})
