import React, { useState, useEffect } from 'react';
import { TrendingUp, TrendingDown, Users, DollarSign, AlertTriangle, ArrowUpRight, ArrowDownRight, Bell, Download, Calendar } from 'lucide-react';

const ExecutiveDashboard = () => {
  const [timeRange, setTimeRange] = useState('30d');
  const [selectedSegment, setSelectedSegment] = useState('all');
  const [showAlerts, setShowAlerts] = useState(true);

  // Mock data for the dashboard
  const kpiData = {
    totalRevenue: {
      current: 2847650,
      previous: 2645320,
      change: 7.6,
      trend: 'up'
    },
    highValueShare: {
      current: 34.2,
      previous: 31.8,
      change: 2.4,
      trend: 'up'
    },
    arpu: {
      current: 1847.50,
      previous: 1792.30,
      change: 3.1,
      trend: 'up'
    },
    churnRisk: {
      current: 187,
      previous: 214,
      change: -12.6,
      trend: 'down'
    },
    segmentVelocity: {
      current: 43,
      previous: 28,
      change: 53.6,
      trend: 'up'
    }
  };

  const alerts = [
    { id: 1, type: 'critical', message: 'VIP segment churn increased 5.2% in last 24h', time: '2h ago' },
    { id: 2, type: 'warning', message: 'Average purchase amount dropped in New Customers segment', time: '5h ago' },
    { id: 3, type: 'info', message: 'Demographic shift detected in Loyal segment', time: '1d ago' }
  ];

  const revenueBySegment = [
    { month: 'Jan', vip: 850000, bigSpenders: 420000, loyal: 280000, standard: 180000, newCustomers: 120000 },
    { month: 'Feb', vip: 920000, bigSpenders: 450000, loyal: 310000, standard: 195000, newCustomers: 140000 },
    { month: 'Mar', vip: 980000, bigSpenders: 480000, loyal: 340000, standard: 210000, newCustomers: 155000 },
    { month: 'Apr', vip: 1050000, bigSpenders: 520000, loyal: 370000, standard: 225000, newCustomers: 170000 },
    { month: 'May', vip: 1120000, bigSpenders: 550000, loyal: 390000, standard: 240000, newCustomers: 180000 },
    { month: 'Jun', vip: 1180000, bigSpenders: 580000, loyal: 410000, standard: 255000, newCustomers: 195000 }
  ];

  const segmentMetrics = [
    { segment: 'VIP', cac: 450, cltv: 8500, roi: 18.9, color: '#FFD700' },
    { segment: 'Big Spenders', cac: 320, cltv: 4200, roi: 13.1, color: '#FF6B6B' },
    { segment: 'Loyal', cac: 180, cltv: 2800, roi: 15.6, color: '#4ECDC4' },
    { segment: 'Standard', cac: 120, cltv: 1200, roi: 10.0, color: '#95E1D3' },
    { segment: 'New Customers', cac: 200, cltv: 800, roi: 4.0, color: '#C7CEEA' }
  ];

  const migrationData = {
    previous: {
      vip: 450,
      bigSpenders: 620,
      loyal: 890,
      standard: 1240,
      newCustomers: 380,
      churned: 0
    },
    current: {
      vip: 485,
      bigSpenders: 658,
      loyal: 912,
      standard: 1198,
      newCustomers: 342,
      churned: 85
    },
    flows: [
      { from: 'newCustomers', to: 'standard', count: 142 },
      { from: 'standard', to: 'loyal', count: 78 },
      { from: 'loyal', to: 'bigSpenders', count: 52 },
      { from: 'bigSpenders', to: 'vip', count: 35 },
      { from: 'standard', to: 'churned', count: 42 },
      { from: 'loyal', to: 'churned', count: 28 },
      { from: 'bigSpenders', to: 'churned', count: 15 }
    ]
  };

  const forecastData = [
    { quarter: 'Q2 2026', revenue: 2847650, forecast: false },
    { quarter: 'Q3 2026', revenue: 3125000, forecast: true },
    { quarter: 'Q4 2026', revenue: 3380000, forecast: true },
    { quarter: 'Q1 2027', revenue: 3520000, forecast: true }
  ];

  const KPICard = ({ title, value, change, trend, icon: Icon, format = 'number' }) => {
    const isPositive = trend === 'up' ? change > 0 : change < 0;
    const displayChange = Math.abs(change);

    return (
      <div className="bg-white rounded-lg shadow-lg p-6 border-l-4" style={{ borderLeftColor: isPositive ? '#10b981' : '#ef4444' }}>
        <div className="flex items-center justify-between mb-2">
          <span className="text-gray-600 text-sm font-medium">{title}</span>
          <Icon className="text-gray-400" size={20} />
        </div>
        <div className="flex items-end justify-between">
          <div>
            <div className="text-3xl font-bold text-gray-900">
              {format === 'currency' && '$'}
              {format === 'number' ? value.toLocaleString() : value.toLocaleString()}
              {format === 'percent' && '%'}
            </div>
            <div className={`flex items-center mt-2 text-sm ${isPositive ? 'text-green-600' : 'text-red-600'}`}>
              {isPositive ? <TrendingUp size={16} className="mr-1" /> : <TrendingDown size={16} className="mr-1" />}
              <span className="font-semibold">{displayChange.toFixed(1)}%</span>
              <span className="text-gray-500 ml-1">vs last month</span>
            </div>
          </div>
        </div>
      </div>
    );
  };

  const StackedAreaChart = () => {
    const maxRevenue = Math.max(...revenueBySegment.map(m => 
      m.vip + m.bigSpenders + m.loyal + m.standard + m.newCustomers
    ));

    return (
      <div className="bg-white rounded-lg shadow-lg p-6">
        <h3 className="text-lg font-semibold mb-4">Revenue by Segment Over Time</h3>
        <div className="relative h-64">
          <svg width="100%" height="100%" viewBox="0 0 600 250">
            {revenueBySegment.map((month, i) => {
              const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
              const total = month.vip + month.bigSpenders + month.loyal + month.standard + month.newCustomers;
              
              let y1 = 200 - (month.vip / maxRevenue * 180);
              let y2 = 200 - ((month.vip + month.bigSpenders) / maxRevenue * 180);
              let y3 = 200 - ((month.vip + month.bigSpenders + month.loyal) / maxRevenue * 180);
              let y4 = 200 - ((month.vip + month.bigSpenders + month.loyal + month.standard) / maxRevenue * 180);
              let y5 = 200 - (total / maxRevenue * 180);

              return (
                <g key={i}>
                  <text x={x} y={220} textAnchor="middle" className="text-xs fill-gray-600">{month.month}</text>
                </g>
              );
            })}
            
            <path
              d={revenueBySegment.map((m, i) => {
                const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
                const total = m.vip + m.bigSpenders + m.loyal + m.standard + m.newCustomers;
                const y = 200 - (total / maxRevenue * 180);
                return `${i === 0 ? 'M' : 'L'} ${x},${y}`;
              }).join(' ') + ` L ${590},200 L 40,200 Z`}
              fill="#C7CEEA"
              opacity="0.8"
            />
            
            <path
              d={revenueBySegment.map((m, i) => {
                const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
                const y = 200 - ((m.vip + m.bigSpenders + m.loyal + m.standard) / maxRevenue * 180);
                return `${i === 0 ? 'M' : 'L'} ${x},${y}`;
              }).join(' ') + ` L ${590},200 L 40,200 Z`}
              fill="#95E1D3"
              opacity="0.8"
            />
            
            <path
              d={revenueBySegment.map((m, i) => {
                const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
                const y = 200 - ((m.vip + m.bigSpenders + m.loyal) / maxRevenue * 180);
                return `${i === 0 ? 'M' : 'L'} ${x},${y}`;
              }).join(' ') + ` L ${590},200 L 40,200 Z`}
              fill="#4ECDC4"
              opacity="0.8"
            />
            
            <path
              d={revenueBySegment.map((m, i) => {
                const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
                const y = 200 - ((m.vip + m.bigSpenders) / maxRevenue * 180);
                return `${i === 0 ? 'M' : 'L'} ${x},${y}`;
              }).join(' ') + ` L ${590},200 L 40,200 Z`}
              fill="#FF6B6B"
              opacity="0.8"
            />
            
            <path
              d={revenueBySegment.map((m, i) => {
                const x = (i / (revenueBySegment.length - 1)) * 550 + 40;
                const y = 200 - (m.vip / maxRevenue * 180);
                return `${i === 0 ? 'M' : 'L'} ${x},${y}`;
              }).join(' ') + ` L ${590},200 L 40,200 Z`}
              fill="#FFD700"
              opacity="0.8"
            />
          </svg>
          
          <div className="flex justify-center gap-4 mt-4 flex-wrap">
            {['VIP', 'Big Spenders', 'Loyal', 'Standard', 'New Customers'].map((label, i) => (
              <div key={i} className="flex items-center">
                <div className="w-3 h-3 rounded mr-2" style={{ 
                  backgroundColor: ['#FFD700', '#FF6B6B', '#4ECDC4', '#95E1D3', '#C7CEEA'][i] 
                }}></div>
                <span className="text-xs text-gray-600">{label}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    );
  };

  const CACvsCLTVChart = () => {
    return (
      <div className="bg-white rounded-lg shadow-lg p-6">
        <h3 className="text-lg font-semibold mb-4">CAC vs CLTV by Segment</h3>
        <div className="space-y-4">
          {segmentMetrics.map((segment, i) => (
            <div key={i} className="relative">
              <div className="flex items-center justify-between mb-2">
                <span className="text-sm font-medium" style={{ color: segment.color }}>{segment.segment}</span>
                <span className="text-xs text-gray-500">ROI: {segment.roi}x</span>
              </div>
              <div className="flex gap-2">
                <div className="flex-1">
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-gray-600">CAC</span>
                    <span className="text-xs font-semibold">${segment.cac}</span>
                  </div>
                  <div className="h-6 bg-red-100 rounded relative overflow-hidden">
                    <div 
                      className="h-full bg-red-500 rounded"
                      style={{ width: `${(segment.cac / 500) * 100}%` }}
                    ></div>
                  </div>
                </div>
                <div className="flex-1">
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-gray-600">CLTV</span>
                    <span className="text-xs font-semibold">${segment.cltv}</span>
                  </div>
                  <div className="h-6 bg-green-100 rounded relative overflow-hidden">
                    <div 
                      className="h-full bg-green-500 rounded"
                      style={{ width: `${(segment.cltv / 9000) * 100}%` }}
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  };

  const SankeyDiagram = () => {
    return (
      <div className="bg-white rounded-lg shadow-lg p-6">
        <h3 className="text-lg font-semibold mb-4">Segment Migration Flow (Last 30 Days)</h3>
        <div className="relative h-96 flex items-center">
          <svg width="100%" height="100%" viewBox="0 0 800 400">
            {/* Left nodes */}
            <g>
              <rect x="50" y="50" width="120" height="40" fill="#FFD700" opacity="0.8" rx="4" />
              <text x="110" y="75" textAnchor="middle" className="text-sm font-medium fill-white">VIP (450)</text>
              
              <rect x="50" y="110" width="120" height="50" fill="#FF6B6B" opacity="0.8" rx="4" />
              <text x="110" y="140" textAnchor="middle" className="text-sm font-medium fill-white">Big Spenders (620)</text>
              
              <rect x="50" y="180" width="120" height="60" fill="#4ECDC4" opacity="0.8" rx="4" />
              <text x="110" y="215" textAnchor="middle" className="text-sm font-medium fill-white">Loyal (890)</text>
              
              <rect x="50" y="260" width="120" height="70" fill="#95E1D3" opacity="0.8" rx="4" />
              <text x="110" y="300" textAnchor="middle" className="text-sm font-medium fill-white">Standard (1,240)</text>
              
              <rect x="50" y="350" width="120" height="30" fill="#C7CEEA" opacity="0.8" rx="4" />
              <text x="110" y="370" textAnchor="middle" className="text-xs font-medium fill-white">New (380)</text>
            </g>

            {/* Right nodes */}
            <g>
              <rect x="630" y="50" width="120" height="45" fill="#FFD700" opacity="0.8" rx="4" />
              <text x="690" y="77" textAnchor="middle" className="text-sm font-medium fill-white">VIP (485)</text>
              
              <rect x="630" y="110" width="120" height="52" fill="#FF6B6B" opacity="0.8" rx="4" />
              <text x="690" y="141" textAnchor="middle" className="text-sm font-medium fill-white">Big Spenders (658)</text>
              
              <rect x="630" y="177" width="120" height="62" fill="#4ECDC4" opacity="0.8" rx="4" />
              <text x="690" y="213" textAnchor="middle" className="text-sm font-medium fill-white">Loyal (912)</text>
              
              <rect x="630" y="254" width="120" height="68" fill="#95E1D3" opacity="0.8" rx="4" />
              <text x="690" y="293" textAnchor="middle" className="text-sm font-medium fill-white">Standard (1,198)</text>
              
              <rect x="630" y="337" width="120" height="28" fill="#C7CEEA" opacity="0.8" rx="4" />
              <text x="690" y="356" textAnchor="middle" className="text-xs font-medium fill-white">New (342)</text>
              
              <rect x="630" y="375" width="120" height="20" fill="#EF4444" opacity="0.8" rx="4" />
              <text x="690" y="389" textAnchor="middle" className="text-xs font-medium fill-white">Churned (85)</text>
            </g>

            {/* Flow paths */}
            <path d="M 170 365 Q 400 365, 630 351" fill="none" stroke="#C7CEEA" strokeWidth="20" opacity="0.4" />
            <path d="M 170 295 Q 400 295, 630 288" fill="none" stroke="#95E1D3" strokeWidth="12" opacity="0.4" />
            <path d="M 170 210 Q 400 210, 630 208" fill="none" stroke="#4ECDC4" strokeWidth="8" opacity="0.4" />
            <path d="M 170 135 Q 400 135, 630 136" fill="none" stroke="#FF6B6B" strokeWidth="6" opacity="0.4" />
            <path d="M 170 70 Q 400 70, 630 72" fill="none" stroke="#FFD700" strokeWidth="5" opacity="0.4" />
            
            {/* Churn flows */}
            <path d="M 170 310 Q 400 350, 630 385" fill="none" stroke="#EF4444" strokeWidth="6" opacity="0.3" />
            <path d="M 170 225 Q 400 320, 630 387" fill="none" stroke="#EF4444" strokeWidth="4" opacity="0.3" />
            <path d="M 170 145 Q 400 290, 630 388" fill="none" stroke="#EF4444" strokeWidth="2" opacity="0.3" />
          </svg>
        </div>
        <div className="mt-4 p-4 bg-blue-50 rounded-lg">
          <p className="text-sm text-gray-700"><strong>Key Insights:</strong> 142 customers upgraded from New to Standard, while 85 customers churned (42 from Standard, 28 from Loyal, 15 from Big Spenders). Focus retention efforts on at-risk segments.</p>
        </div>
      </div>
    );
  };

  const ForecastChart = () => {
    return (
      <div className="bg-white rounded-lg shadow-lg p-6">
        <h3 className="text-lg font-semibold mb-4">Revenue Forecast (Next 9 Months)</h3>
        <div className="relative h-64">
          <svg width="100%" height="100%" viewBox="0 0 600 250">
            {forecastData.map((q, i) => {
              const x = (i / (forecastData.length - 1)) * 550 + 40;
              const y = 200 - ((q.revenue / 4000000) * 180);
              
              return (
                <g key={i}>
                  <circle 
                    cx={x} 
                    cy={y} 
                    r="6" 
                    fill={q.forecast ? '#93C5FD' : '#3B82F6'} 
                    stroke={q.forecast ? '#3B82F6' : 'none'}
                    strokeWidth="2"
                    strokeDasharray={q.forecast ? '2,2' : 'none'}
                  />
                  <text x={x} y={220} textAnchor="middle" className="text-xs fill-gray-600">{q.quarter}</text>
                  <text x={x} y={y - 15} textAnchor="middle" className="text-xs fill-gray-700 font-semibold">
                    ${(q.revenue / 1000000).toFixed(2)}M
                  </text>
                  {i > 0 && (
                    <line 
                      x1={(i - 1) / (forecastData.length - 1) * 550 + 40} 
                      y1={200 - ((forecastData[i-1].revenue / 4000000) * 180)}
                      x2={x}
                      y2={y}
                      stroke={q.forecast ? '#93C5FD' : '#3B82F6'}
                      strokeWidth="2"
                      strokeDasharray={q.forecast ? '5,5' : 'none'}
                    />
                  )}
                </g>
              );
            })}
            
            <line x1="40" y1="200" x2="590" y2="200" stroke="#E5E7EB" strokeWidth="1" />
          </svg>
          
          <div className="flex justify-center gap-6 mt-4">
            <div className="flex items-center">
              <div className="w-4 h-1 bg-blue-600 mr-2"></div>
              <span className="text-xs text-gray-600">Actual</span>
            </div>
            <div className="flex items-center">
              <div className="w-4 h-1 bg-blue-300 border-dashed border-t-2 border-blue-600 mr-2"></div>
              <span className="text-xs text-gray-600">Forecast</span>
            </div>
          </div>
        </div>
        <div className="mt-4 p-4 bg-green-50 rounded-lg">
          <p className="text-sm text-gray-700"><strong>Projection:</strong> Revenue expected to grow 23% to $3.52M by Q1 2027, driven by VIP segment expansion and improved retention rates.</p>
        </div>
      </div>
    );
  };

  const ChurnHeatmap = () => {
    const heatmapData = [
      { segment: 'VIP', risk: 15, color: '#10b981' },
      { segment: 'Big Spenders', risk: 32, color: '#fbbf24' },
      { segment: 'Loyal', risk: 28, color: '#fbbf24' },
      { segment: 'Standard', risk: 64, color: '#ef4444' },
      { segment: 'New Customers', risk: 48, color: '#f97316' }
    ];

    return (
      <div className="bg-white rounded-lg shadow-lg p-6">
        <h3 className="text-lg font-semibold mb-4">Churn Probability Heatmap</h3>
        <div className="space-y-3">
          {heatmapData.map((item, i) => (
            <div key={i}>
              <div className="flex items-center justify-between mb-1">
                <span className="text-sm font-medium text-gray-700">{item.segment}</span>
                <span className="text-sm font-semibold" style={{ color: item.color }}>
                  {item.risk}% risk
                </span>
              </div>
              <div className="h-8 bg-gray-100 rounded-lg relative overflow-hidden">
                <div 
                  className="h-full rounded-lg transition-all duration-500"
                  style={{ 
                    width: `${item.risk}%`,
                    backgroundColor: item.color
                  }}
                ></div>
              </div>
            </div>
          ))}
        </div>
        <div className="mt-4 p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
          <p className="text-sm text-gray-700"><strong>Alert:</strong> Standard segment shows 64% churn probability. Immediate intervention required with targeted retention campaigns.</p>
        </div>
      </div>
    );
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 shadow-lg">
        <div className="max-w-7xl mx-auto">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold mb-2">Executive Dashboard</h1>
              <p className="text-blue-100">Customer Segmentation Intelligence</p>
            </div>
            <div className="flex items-center gap-4">
              <select 
                value={timeRange} 
                onChange={(e) => setTimeRange(e.target.value)}
                className="bg-white text-gray-800 px-4 py-2 rounded-lg text-sm font-medium"
              >
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="1y">Last Year</option>
              </select>
              <button className="bg-white text-blue-600 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium hover:bg-blue-50 transition">
                <Download size={16} />
                Export
              </button>
              <button 
                onClick={() => setShowAlerts(!showAlerts)}
                className="relative bg-blue-700 p-2 rounded-lg hover:bg-blue-600 transition"
              >
                <Bell size={20} />
                {alerts.length > 0 && (
                  <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                    {alerts.length}
                  </span>
                )}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto p-6">
        {/* Alerts Panel */}
        {showAlerts && alerts.length > 0 && (
          <div className="mb-6 bg-white rounded-lg shadow-lg p-4 border-l-4 border-red-500">
            <h3 className="font-semibold mb-3 flex items-center gap-2">
              <AlertTriangle className="text-red-500" size={20} />
              Active Alerts
            </h3>
            <div className="space-y-2">
              {alerts.map(alert => (
                <div key={alert.id} className={`p-3 rounded-lg flex items-start justify-between ${
                  alert.type === 'critical' ? 'bg-red-50' : 
                  alert.type === 'warning' ? 'bg-yellow-50' : 'bg-blue-50'
                }`}>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-800">{alert.message}</p>
                    <p className="text-xs text-gray-500 mt-1">{alert.time}</p>
                  </div>
                  <button className="text-gray-400 hover:text-gray-600">
                    <span className="text-xl">&times;</span>
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* KPI Ticker Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
          <KPICard
            title="Total Addressable Revenue"
            value={kpiData.totalRevenue.current}
            change={kpiData.totalRevenue.change}
            trend={kpiData.totalRevenue.trend}
            icon={DollarSign}
            format="currency"
          />
          <KPICard
            title="High-Value Segment Share"
            value={kpiData.highValueShare.current}
            change={kpiData.highValueShare.change}
            trend={kpiData.highValueShare.trend}
            icon={TrendingUp}
            format="percent"
          />
          <KPICard
            title="Average Revenue Per User"
            value={kpiData.arpu.current}
            change={kpiData.arpu.change}
            trend={kpiData.arpu.trend}
            icon={Users}
            format="currency"
          />
          <KPICard
            title="Cluster Churn Risk"
            value={kpiData.churnRisk.current}
            change={kpiData.churnRisk.change}
            trend="down"
            icon={AlertTriangle}
            format="number"
          />
          <KPICard
            title="Segment Velocity"
            value={kpiData.segmentVelocity.current}
            change={kpiData.segmentVelocity.change}
            trend={kpiData.segmentVelocity.trend}
            icon={ArrowUpRight}
            format="number"
          />
        </div>

        {/* Main Visualizations */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div className="lg:col-span-2">
            <StackedAreaChart />
          </div>
          <CACvsCLTVChart />
          <ChurnHeatmap />
        </div>

        <div className="mb-6">
          <SankeyDiagram />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-1 gap-6">
          <ForecastChart />
        </div>

        {/* Footer Insights */}
        <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border-l-4 border-green-500">
            <h4 className="font-semibold text-green-800 mb-2">Growth Driver</h4>
            <p className="text-sm text-green-700">VIP segment grew 7.8% this month, contributing 41% of total revenue. Continue premium tier initiatives.</p>
          </div>
          <div className="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border-l-4 border-yellow-500">
            <h4 className="font-semibold text-yellow-800 mb-2">Attention Needed</h4>
            <p className="text-sm text-yellow-700">Standard segment churn elevated at 64%. Deploy retention campaign with personalized offers.</p>
          </div>
          <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border-l-4 border-blue-500">
            <h4 className="font-semibold text-blue-800 mb-2">Opportunity</h4>
            <p className="text-sm text-blue-700">43 customers moved up tiers this month. Optimize upsell strategy to capture more momentum.</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ExecutiveDashboard;
