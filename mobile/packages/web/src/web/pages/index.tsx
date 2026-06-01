import { useQuery } from "@tanstack/react-query";
import { api } from "../lib/api";
import { useDesktop } from "../hooks/use-desktop";

function Index() {
  const health = useQuery({
    queryKey: ["health"],
    queryFn: async () => {
      const res = await api.health.$get();
      return res.json();
    },
  });
  const desktop = useDesktop();

  return (
    <div>
      <h1>Welcome</h1>
      <p>Platform: {desktop ? `Desktop (${desktop.platform})` : "Web"}</p>
      <p>
        API Status:{" "}
        {health.isLoading
          ? "Loading..."
          : health.isError
            ? "Error"
            : health.data?.status}
      </p>
    </div>
  );
}

export default Index;
