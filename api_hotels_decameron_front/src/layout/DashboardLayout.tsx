import LogoutButton from "../auth/LogoutButton";

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="flex min-h-screen">
            <aside className="w-64 shrink-0 bg-gray-800 text-white p-4">
                <h2 className="text-lg font-bold mb-6">Decameron</h2>
                <nav className="space-y-2">
                    <a href="/" className="block py-2 px-2 rounded hover:bg-gray-700">🏨 Hoteles</a>
                </nav>
                <div className="mt-auto">
                    <LogoutButton />
                </div>
            </aside>
            <main className="flex-1 p-6 bg-gray-100">{children}</main>
        </div>
    );
}
