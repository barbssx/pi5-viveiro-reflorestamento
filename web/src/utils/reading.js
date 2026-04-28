function toNumber(value) {
  const numericValue = Number(value)
  return Number.isFinite(numericValue) ? numericValue : null
}

function formatMetricValue(value) {
  const numericValue = toNumber(value)

  if (numericValue === null) {
    return '--'
  }

  return Number.isInteger(numericValue) ? `${numericValue}` : numericValue.toFixed(1)
}

function getSoilMoistureTone(value) {
  const numericValue = toNumber(value)

  if (numericValue === null) {
    return 'neutral'
  }

  if (numericValue < 35) {
    return 'danger'
  }

  if (numericValue < 55) {
    return 'warning'
  }

  return 'success'
}

function getAirTemperatureTone(value) {
  const numericValue = toNumber(value)

  if (numericValue === null) {
    return 'neutral'
  }

  if (numericValue < 12 || numericValue > 32) {
    return 'danger'
  }

  if (numericValue < 18 || numericValue > 28) {
    return 'warning'
  }

  return 'success'
}

function getAirHumidityTone(value) {
  const numericValue = toNumber(value)

  if (numericValue === null) {
    return 'neutral'
  }

  if (numericValue < 35) {
    return 'danger'
  }

  if (numericValue < 50) {
    return 'warning'
  }

  return 'success'
}

function getIrrigationTone(reading) {
  const soilTone = getSoilMoistureTone(reading?.soil_moisture)

  if (reading?.irrigation_status) {
    return 'success'
  }

  if (soilTone === 'danger') {
    return 'danger'
  }

  if (soilTone === 'warning') {
    return 'warning'
  }

  return 'neutral'
}

export function formatReadingTimestamp(value) {
  if (!value) {
    return '--'
  }

  return new Date(value).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

export function getReadingMetrics(reading) {
  return [
    {
      id: 'soil-moisture',
      label: 'Umidade do solo',
      value: formatMetricValue(reading?.soil_moisture),
      unit: '%',
      tone: getSoilMoistureTone(reading?.soil_moisture),
      helper: 'Base principal para acompanhar a necessidade de irrigacao.',
    },
    {
      id: 'air-temperature',
      label: 'Temperatura do ar',
      value: formatMetricValue(reading?.air_temperature),
      unit: '°C',
      tone: getAirTemperatureTone(reading?.air_temperature),
      helper: 'Indica variacoes que podem afetar crescimento e manejo.',
    },
    {
      id: 'air-humidity',
      label: 'Umidade do ar',
      value: formatMetricValue(reading?.air_humidity),
      unit: '%',
      tone: getAirHumidityTone(reading?.air_humidity),
      helper: 'Ajuda a identificar cenarios de estresse ambiental.',
    },
    {
      id: 'irrigation-status',
      label: 'Irrigacao',
      value: reading?.irrigation_status ? 'Ligada' : 'Desligada',
      unit: '',
      tone: getIrrigationTone(reading),
      helper: 'Estado atual da rotina automatizada de irrigacao.',
    },
  ]
}

export function getReadingChips(reading) {
  const soilTone = getSoilMoistureTone(reading?.soil_moisture)
  const temperatureTone = getAirTemperatureTone(reading?.air_temperature)
  const airHumidityTone = getAirHumidityTone(reading?.air_humidity)
  const irrigationTone = getIrrigationTone(reading)

  return [
    {
      id: 'soil',
      label:
        soilTone === 'danger'
          ? 'Solo seco'
          : soilTone === 'warning'
            ? 'Solo em observacao'
            : 'Solo equilibrado',
      tone: soilTone,
    },
    {
      id: 'temperature',
      label:
        temperatureTone === 'danger'
          ? 'Temperatura critica'
          : temperatureTone === 'warning'
            ? 'Temperatura em atencao'
            : 'Temperatura estavel',
      tone: temperatureTone,
    },
    {
      id: 'air',
      label:
        airHumidityTone === 'danger'
          ? 'Ar seco'
          : airHumidityTone === 'warning'
            ? 'Umidade moderada'
            : 'Umidade adequada',
      tone: airHumidityTone,
    },
    {
      id: 'irrigation',
      label: reading?.irrigation_status ? 'Irrigacao ativa' : 'Irrigacao inativa',
      tone: irrigationTone,
    },
  ]
}

export function getReadingHeadline(reading) {
  const soilTone = getSoilMoistureTone(reading?.soil_moisture)
  const irrigationTone = getIrrigationTone(reading)

  if (soilTone === 'danger' && irrigationTone === 'danger') {
    return 'A irrigacao merece prioridade: o solo indica baixa umidade no ultimo registro.'
  }

  if (reading?.irrigation_status) {
    return 'O sistema indica irrigacao ativa, mantendo o viveiro assistido no momento.'
  }

  if (soilTone === 'warning') {
    return 'O viveiro esta estavel, mas a umidade do solo pede acompanhamento de curto prazo.'
  }

  return 'O ultimo registro aponta um ambiente monitorado e pronto para acompanhamento continuo.'
}

export function getIrrigationPresentation(reading) {
  const tone = getIrrigationTone(reading)

  if (reading?.irrigation_status) {
    return {
      label: 'Irrigacao ligada',
      description: 'A rotina automatizada esta em operacao no momento desta leitura.',
      tone,
    }
  }

  if (tone === 'danger') {
    return {
      label: 'Irrigacao desligada',
      description: 'O sistema esta desligado apesar da umidade do solo indicar atencao imediata.',
      tone,
    }
  }

  return {
    label: 'Irrigacao desligada',
    description: 'Nao houve acionamento da irrigacao no ultimo registro coletado.',
    tone,
  }
}
